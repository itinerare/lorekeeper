<?php namespace App\Services\Item;

use App\Services\Service;

use DB;

use App\Services\InventoryManager;
use App\Services\CharacterManager;

use App\Models\Frame\FrameCategory;
use App\Models\Frame\Frame;
use App\Models\Character\Character;

class FrameService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Frame Service
    |--------------------------------------------------------------------------
    |
    | Handles the editing and usage of frame type items.
    |
    */

    /**
     * Retrieves any data that should be used in the item tag editing form.
     * Optgroup code is modified from draginraptor's organized trait dropdown extension.
     *
     * @return array
     */
    public function getEditData()
    {
        $sortedCategories = collect(FrameCategory::all()->sortBy('sort')->pluck('name', 'name')->toArray());

        $grouped = Frame::where('is_default', 0)
            ->select('name', 'id', 'frame_category_id')->with('category')
            ->orderBy('name')->get()->keyBy('id')
            ->groupBy('category.name', $preserveKeys = true)->toArray();

        if(isset($grouped[""])) {
            if(!$sortedCategories->contains('Miscellaneous'))
                $sortedCategories->put('Miscellaneous', 'Miscellaneous');
            $grouped['Miscellaneous'] = $grouped['Miscellaneous'] ?? [] + $grouped[""];
        }

        $sortedCategories = $sortedCategories->filter(function($value, $key) use($grouped) {
            return in_array($value, array_keys($grouped), true);
        });

        foreach($grouped as $category=>$frames)
            foreach($frames as $id=>$frame) $grouped[$category][$id] = $frame["name"];

        $sortedFrames = $sortedCategories->map(function($category, $key) use($grouped) {
            return $grouped[$category];
        });

        return [
            'frames' => $sortedFrames,
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
        return $tag->data;
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
            $tag->update(['data' => $data['frame_id']]);

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

                // Check that the character exists
                $character = Character::where('id', $data['frame_character_id'])->first();
                if(!$character) throw new \Exception('Invalid character selected.');

                // Next, try to delete the box item. If successful, we can start distributing rewards.
                if((new InventoryManager)->debitStack($stack->user, 'Frame Unlocked', ['data' => 'Unlocked for '.$character->displayName], $stack, $data['quantities'][$key])) {

                    for($q=0; $q<$data['quantities'][$key]; $q++) {
                        // Distribute character rewards
                        $service = new CharacterManager;
                        if(!$service->unlockCharacterFrame($character, ['log_data' => 'Received from using '.$stack->item->name, 'frame_id' => $stack->item->tag('frame')->data], $user)) {
                            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
                            throw new \Exception("Failed to unlock frame.");
                        }
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
