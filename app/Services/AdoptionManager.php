<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;

use App\Models\Character\Character;
use App\Models\Adoption\Adoption;
use App\Models\Adoption\AdoptionStock;
use App\Models\Adoption\AdoptionLog;
use App\Models\Adoption\AdoptionCurrency;
use App\Models\User\User;
use App\Services\CharacterManager;

class AdoptionManager extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Adoption Manager
    |--------------------------------------------------------------------------
    |
    | Handles purchasing of characters from adoptions.
    |
    */

    /**
     * Buys an character from a adoption.
     *
     * @param  array                 $data
     * @param  \App\Models\User\User $user
     * @return bool|App\Models\Adoption\Adoption
     */
    public function buyStock($data, $user)
    {
        DB::beginTransaction();

        try {
            // Check that the adoption exists and is open
            $adoption = Adoption::where('id', $data['adoption_id'])->where('is_active', 1)->first();
            if(!$adoption) throw new \Exception("Invalid adoption selected.");

            // Check that the stock exists and belongs to the adoption
            $adoptionStock = AdoptionStock::where('id', $data['stock_id'])->where('adoption_id', $data['adoption_id'])->with('currency')->with('character')->first();
            $adoptionCurrency = AdoptionCurrency::where('stock_id', $data['stock_id'])->where('currency_id', $data['currency_id'])->first();
            if(!$adoptionStock) throw new \Exception("Invalid character selected.");

            // Check if the character has a quantity, and if it does, check there is stock remaining
            if($adoptionStock->is_limited_stock && $adoptionStock->quantity < 1) throw new \Exception("This character is out of stock.");

            $character = null;
            if($data['bank'] == 'character')
            {
                // Check if the user is using a character to pay
                // - stock must be purchaseable with characters
                // - currency must be character-held
                // - character has enough currency
                if(!$adoptionStock->use_character_bank || !$adoptionCurrency->currency->is_character_owned) throw new \Exception("You cannot use a character's bank to pay for this character.");
                if(!$data['slug']) throw new \Exception("Please enter a character code.");
                $character = Character::where('slug', $data['slug'])->first();
                if(!$character) throw new \Exception("Please enter a valid character code.");
                if(!(new CurrencyManager)->debitCurrency($character, null, 'Adoption Purchase', 'Purchased '.$adoptionStock->character->slug.' from '.$adoption->name, $adoptionCurrency->currency, $adoptionCurrency->cost)) throw new \Exception("Not enough currency to make this purchase.");
            }
            else
            {
                // If the user is paying by themselves
                // - stock must be purchaseable by users
                // - currency must be user-held
                // - user has enough currency
                if(!$adoptionStock->use_user_bank || !$adoptionCurrency->currency->is_user_owned) throw new \Exception("You cannot use your user bank to pay for this character.");
                if($adoptionCurrency->cost > 0 && !(new CurrencyManager)->debitCurrency($user, null, 'Adoption Purchase', 'Purchased '.$adoptionStock->character->slug.' from '.$adoption->name, $adoptionCurrency->currency, $adoptionCurrency->cost)) throw new \Exception("Not enough currency to make this purchase.");
            }

            // If the character has a limited quantity, decrease the quantity
            if($adoptionStock->is_limited_stock) 
            {
                $adoptionStock->quantity--;
                $adoptionStock->save();
            }

            $quantity = 1;

            // Add a purchase log
            $adoptionLog = AdoptionLog::create([
                'adoption_id' => $adoption->id, 
                'character_id' => $character ? $character->id : null, 
                'user_id' => $user->id, 
                'currency_id' => $adoptionCurrency->currency->id, 
                'cost' => $adoptionCurrency->cost, 
                'adopt_id' => $adoptionStock->character_id, 
                'quantity' => $quantity
            ]);
            
            // doing stuff manually because why not and because hopefully no one ever looks here
            $data = [];
            $data['recipient_id'] = $user->id;
            $data['reason'] = 'Bought from adoption center';
            $adopt = Character::find($adoptionStock->character_id);
            $admin = User::find(1);
            
            if(!(new CharacterManager)->adminTransfer($data, $adopt, $admin)) {
                throw new \Exception("Failed to transfer character.");
            }

            $adoptionStock->delete();
            $adoptionCurrency->delete();

            return $this->commitReturn($adoption);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Checks if the purchase limit for an character from a shop has been reached.
     *
     * @param  \App\Models\Shop\ShopStock  $shopStock
     * @param  \App\Models\User\User      $user
     * @return bool
     */
    public function checkPurchaseLimitReached($shopStock, $user)
    {
        if($shopStock->purchase_limit > 0)
            return (ShopLog::where('shop_id', $shopStock->shop_id)->where('character_id', $shopStock->character_id)->where('user_id', $user->id)->sum('quantity') >= $shopStock->purchase_limit);
        return false;
    }
}