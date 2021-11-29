<?php namespace App\Services;

use DB;

use Config;
use App\Services\Service;

use App\Models\Status\StatusEffect;
use App\Models\Character\CharacterStatusEffect;


class StatusEffectService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Status Effect Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of status effects.
    |
    */

    /**
     * Creates a new currency.
     *
     * @param  array                  $data
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\Status\StatusEffect
     */
    public function createStatusEffect($data, $user)
    {
        DB::beginTransaction();

        try {
            $data = $this->populateData($data);

            $image = null;
            if(isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }
            else $data['has_image'] = 0;

            $status = StatusEffect::create($data);

            if($image) $this->handleImage($image, $status->imagePath, $status->imageFileName);

            return $this->commitReturn($status);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Updates a status effect.
     *
     * @param  \App\Models\Status\StatusEffect  $status
     * @param  array                            $data
     * @param  \App\Models\User\User            $user
     * @return bool|\App\Models\Status\StatusEffect
     */
    public function updateStatusEffect($status, $data, $user)
    {
        DB::beginTransaction();

        try {
            // More specific validation
            if(StatusEffect::where('name', $data['name'])->where('id', '!=', $status->id)->exists()) throw new \Exception("The name has already been taken.");

            $data = $this->populateData($data, $status);

            $image = null;
            if(isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }
            else $data['has_image'] = 0;

            $status->update($data);

            if($image) $this->handleImage($image, $status->imagePath, $status->imageFileName);

            return $this->commitReturn($status);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Processes user input for creating/updating a status effect.
     *
     * @param  array                             $data
     * @param  \App\Models\Status\StatusEffect   $status
     * @return array
     */
    private function populateData($data, $status = null)
    {
        if(isset($data['description']) && $data['description']) $data['parsed_description'] = parse($data['description']);
        else $data['parsed_description'] = null;

        if(isset($data['remove_image'])) {
            if($status) {
                if($status->has_image && $data['remove_image']) {
                    $data['has_image'] = 0;
                    $this->deleteImage($status->imagePath, $status->imageFileName);
                }
            }
            unset($data['remove_image']);
        }

        // Process severity information
        $data['data'] = [];

        if(isset($data['severity_name'])) {
            foreach($data['severity_name'] as $key=>$severity) {
                $data['data'][] = [
                    'name' => $severity,
                    'breakpoint' => $data['severity_breakpoint'][$key],
                ];
            }
        }

        $data['data'] = json_encode($data['data']);

        return $data;
    }

    /**
     * Deletes a status effect.
     *
     * @param  \App\Models\Status\StatusEffect  $status
     * @return bool
     */
    public function deleteStatusEffect($status)
    {
        DB::beginTransaction();

        try {

            if(DB::table('loots')->where('rewardable_type', 'Status')->where('rewardable_id', $status->id)->exists()) throw new \Exception("A loot table currently distributes this status effect as a potential reward. Please remove the status effect before deleting it.");
            if(DB::table('prompt_rewards')->where('rewardable_type', 'Status Effect')->where('rewardable_id', $status->id)->exists()) throw new \Exception("A prompt currently distributes this status effect as a reward. Please remove the status effect before deleting it.");
            if(CharacterStatusEffect::where('status_effect_id', $status->id)->exists()) throw new \Exception('A character currently has this status effect. Please remove the status effect before deleting it.');

            CharacterStatusEffect::where('status_effect_id', $status->id)->delete();
            if($status->has_image) $this->deleteImage($status->imagePath, $status->imageFileName);
            $status->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
}
