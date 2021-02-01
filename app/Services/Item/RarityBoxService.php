<?php namespace App\Services\Item;

use App\Services\Service;

use DB;

use App\Services\InventoryManager;
use App\Models\Item\Item;
use App\Models\Item\ItemTag;

class RarityBoxService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Rarity Box Service
    |--------------------------------------------------------------------------
    |
    | Handles the editing and usage of box type items.
    |
    */

    /**
     * Retrieves any data that should be used in the item tag editing form.
     *
     * @return array
     */
    public function getEditData()
    {
        $rarities = Item::whereNotNull('data')->get()->pluck('rarity')->unique()->toArray();
        sort($rarities);

        return [
            'rarities' => array_filter($rarities),
        ];
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
            if(!isset($data['rarity'])) throw new \Exception('Please select a rarity.');

            $tag->update(['data' => $data['rarity']]);

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

                $tag = ItemTag::where('tag', $data['tag'])->where('item_id', $stack->item->id)->first();

                // Next, try to delete the box item. If successful, we can start distributing rewards.
                if((new InventoryManager)->debitStack($stack->user, 'Rarity Box Opened', ['data' => ''], $stack, $data['quantities'][$key])) {
                    // Fetch item, checking that it exists
                    $item = Item::released()->whereRaw('JSON_EXTRACT(`data`, \'$.rarity\') = ' . $tag->data)->where('id', $data['item_id'])->first();
                    if(!$item) throw new \Exception('The selected item is invalid.');

                    if(!(new InventoryManager)->creditItem($user, $user, 'Rarity Box Rewards', [
                        'data' => 'Received rewards from opening '.$stack->item->name
                    ], $item, $data['quantities'][$key])) throw new \Exception("Failed to open rarity box.");
                    flash("You have received: ".$item->name.' x'.$data['quantities'][$key]);
                }
            }
            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

}
