<?php namespace App\Services\Item;

use App\Services\Service;

use DB;

use App\Models\Status\StatusEffect;
use App\Models\Character\Character;
use App\Services\InventoryManager;

class CureService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Cure Service
    |--------------------------------------------------------------------------
    |
    | Handles the editing and usage of cure type items.
    |
    */

    /**
     * Retrieves any data that should be used in the item tag editing form.
     *
     * @return array
     */
    public function getEditData()
    {
        return [
            'statuses' => StatusEffect::orderBy('name', 'DESC')->pluck('name', 'id'),
        ];
    }

    /**
     * Processes the data attribute of the tag and returns it in the preferred format.
     *
     * @param  string  $tag
     * @return mixed
     */
    public function getTagData($tag)
    {
        $rewards = [];
        if($tag->data) {
            $assets = parseAssetData($tag->data);
            foreach($assets as $type => $a)
            {
                $class = getAssetModelString($type, false);
                foreach($a as $id => $asset)
                {
                    $rewards = (array)[
                        'status_effect_id' => $id,
                        'quantity' => $asset['quantity']
                    ];
                }
            }
        }
        return $rewards;
    }

    /**
     * Processes the data attribute of the tag and returns it in the preferred format.
     *
     * @param  string  $tag
     * @param  array   $data
     * @return bool
     */
    public function updateData($tag, $data)
    {
        DB::beginTransaction();

        try {
            // The data will be stored as an asset table, json_encode()d.
            // First build the asset table, then prepare it for storage.
            $assets = createAssetsArray();
            $asset = StatusEffect::find($data['status_effect_id']);
            addAsset($assets, $asset, ($data['quantity'] > 0 ? -$data['quantity'] : $data['quantity']));

            $assets = getDataReadyAssets($assets);

            $tag->update(['data' => json_encode($assets)]);

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }


    /**
     * Acts upon the item when used from the inventory.
     *
     * @param  \App\Models\User\UserItem  $stacks
     * @param  \App\Models\User\User      $user
     * @param  array                      $data
     * @return bool
     */
    public function act($stacks, $user, $data)
    {
        DB::beginTransaction();

        try {
            foreach($stacks as $key=>$stack) {
                // We don't want to let anyone who isn't the owner of the box open it,
                // so do some validation...
                if($stack->user_id != $user->id) throw new \Exception("This item does not belong to you.");

                $character = Character::where('id', $data['cure_character_id'])->first();
                if(!$character) throw new \Exception('Invalid character selected.');

                // Next, try to delete the item. If successful, we can start distributing rewards.
                if((new InventoryManager)->debitStack($stack->user, 'Cure Applied', ['data' => ''], $stack, $data['quantities'][$key])) {

                    for($q=0; $q<$data['quantities'][$key]; $q++) {
                        // Distribute user rewards
                        if(!$rewards = fillCharacterAssets(parseAssetData($stack->item->tag('cure')->data), $user, $character, 'Cure Applied', [
                            'data' => 'Cured status by using '.$stack->item->name
                        ])) throw new \Exception("Failed to use cure.");
                    }
                }
            }
            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
}
