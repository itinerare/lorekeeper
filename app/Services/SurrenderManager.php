<?php namespace App\Services;

use App\Services\Service;

use Carbon\Carbon;

use DB;
use Config;
use Image;
use Notifications;
use Settings;

use App\Models\User\User;
use App\Models\Character\Character;
use App\Models\Currency\Currency;
use App\Models\Adoption\Surrender;
use App\Models\Adoption\Adoption;
use App\Models\Adoption\AdoptionStock;

use App\Services\CurrencyManager;
use App\Services\AdoptionService;

class SurrenderManager extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Surrender Manager
    |--------------------------------------------------------------------------
    |
    | Handles creation and modification of surrender data.
    |
    */

    /**
     * Creates a new surrender.
     *
     * @param  array                  $data
     * @param  \App\Models\User\User  $user
     * @param  bool                   $isClaim
     * @return mixed
     */
    public function createSurrender($data, $user)
    {
        DB::beginTransaction();

        try {
            // check that surrenders are open
            if(!Settings::get('is_surrenders_open')) throw new \Exception("Surrenders are closed.");

            // might be needed
            $characters = Character::where('user_id', $user->id)->where('id', $data['character_id'])->first();
            
            // Get a list of rewards, then create the surrender itself
            $surrender = Surrender::create([
                'user_id' => $user->id,
                'character_id' => $data['character_id'],
                'worth' => $data['worth'],
                'notes' => $data['notes'],
                'status' => 'Pending',
                ]);

            return $this->commitReturn($surrender);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Rejects a surrender.
     *
     * @param  array                  $data
     * @param  \App\Models\User\User  $user
     * @return mixed
     */
    public function rejectSurrender($data, $user)
    {
        DB::beginTransaction();

        try {
            // 1. check that the surrender exists
            // 2. check that the surrender is pending
            if(!isset($data['surrender'])) $surrender = Surrender::where('status', 'Pending')->where('id', $data['id'])->first();
            elseif($data['surrender']->status == 'Pending') $surrender = $data['surrender'];
            else $surrender = null;
            if(!$surrender) throw new \Exception("Invalid surrender.");

            // The only things we need to set are: 
            // 1. staff comment
            // 2. staff ID
            // 3. status
            $surrender->update([
                'staff_comments' => $data['staff_comments'],
                'staff_id' => $user->id,
                'status' => 'Rejected'
            ]);

            // need to make notifications
            Notifications::create('SURRENDER_REJECTED', $surrender->user, [
                'staff_url' => $user->url,
                'staff_name' => $user->name,
                'surrender_id' => $surrender->id,
            ]);

            return $this->commitReturn($surrender);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Approves a surrender.
     *
     * @param  array                  $data
     * @param  \App\Models\User\User  $user
     * @return mixed
     */
    public function approveSurrender($data, $user)
    {
        DB::beginTransaction();

        try {
            // 1. check that the surrender exists
            // 2. check that the surrender is pending
            $surrender = Surrender::where('status', 'Pending')->where('id', $data['id'])->first();
            if(!$surrender) throw new \Exception("Invalid surrender.");

            if(!$data['currency_id']) {
                throw new \Exception("Please select a currency type.");
            }
            // Distribute user currency
            if(!(new CurrencyManager)->creditCurrency(NULL, $surrender->user, 'Adoption Stock', 'Stock worth', $data['currency_id'], $data['grant'])) { 
                throw new \Exception("Failed to distribute currency to user.");
            }
            // Manual stuff
            $data['recipient_id'] = User::find(1)->id;
            $data['reason'] = 'Surrendered to adoption center';
            $data['use_user_bank'] = 1;
            $data['use_character_bank'] = 1;

            // Edit if you turn on calculate by traits
            if(Settings::get('calculate_by_traits')) {
            $data['cost'] = $data['grant'] + 100;
            }
            else {
            $data['cost'] = $data['grant'];
            }

            $data['character_id'] = $surrender->character_id;
            $adopt = Character::where('id', $surrender->character_id)->first();
            $surrenderer = User::where('id', $surrender->user_id)->first();

            if(AdoptionStock::where('character_id', $surrender->character_id)->exists()) {
                throw new \Exception("This character already exists as stock.");
            }
            // Transfer character to admin
            if(!(new CharacterManager)->adminTransfer($data, $adopt, $surrenderer)) {
                throw new \Exception("Failed to transfer character.");
            }
            // Add character to the adoption stock
            if(!(new AdoptionService)->createAdoptionStock(Adoption::find(1), $data, null)) {
                throw new \Exception("Failed to create stock.");
            }
            
            // Finally, set: 
			// 1. staff comments
            // 2. staff ID
            // 3. status
            $surrender->update([
			    'staff_comments' => $data['staff_comments'],
                'staff_id' => $user->id,
                'status' => 'Approved',
            ]);

            Notifications::create('SURRENDER_APPROVED', $surrender->user, [
                'staff_url' => $user->url,
                'staff_name' => $user->name,
                'surrender_id' => $surrender->id,
            ]);

            return $this->commitReturn($surrender);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
}