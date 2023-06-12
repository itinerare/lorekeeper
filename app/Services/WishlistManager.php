<?php namespace App\Services;

use DB;

use App\Services\Service;

use App\Models\User\Wishlist;
use App\Models\User\WishlistItem;
use App\Models\Item\Item;

class WishlistManager extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Wishlist Manager
    |--------------------------------------------------------------------------
    |
    | Handles creation, modification and usage of user wishlists.
    |
    */

    /**
     * Create a wishlist.
     *
     * @param  array                     $data
     * @param  \App\Models\User\User     $user
     * @return \App\Models\User\Wishlist|bool
     */
    public function createWishlist($data, $user)
    {
        DB::beginTransaction();

        try {
            // Check that the user does not already have a wishlist with this name
            if(Wishlist::where('user_id', $user->id)->where('name', $data['name'])->exists()) throw new \Exception('You have already created a wishlist with this name.');

            // Create the wishlist
            $wishlist = Wishlist::create([
                'user_id' => $user->id,
                'name' => $data['name']
            ]);

            return $this->commitReturn($wishlist);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Update a wishlist.
     *
     * @param  array                     $data
     * @param  \App\Models\User\Wishlist $wishlist
     * @param  \App\Models\User\User     $user
     * @return \App\Models\User\Wishlist|bool
     */
    public function updateWishlist($data, $wishlist, $user)
    {
        DB::beginTransaction();

        try {
            // Check that the wishlist exists and the user can edit it/it belongs to them
            if(!$wishlist) throw new \Exception('Invalid wishlist.');
            if($wishlist->user_id != $user->id) throw new \Exception('This wishlist does not belong to you.');
            // Check that the user does not already have a wishlist with this name
            if(Wishlist::where('user_id', $user->id)->where('name', $data['name'])->exists()) throw new \Exception('You have already created a wishlist with this name.');

            // Update the wishlist
            $wishlist->update($data);

            return $this->commitReturn($wishlist);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Adds an item to a wishlist.
     *
     * @param  \App\Models\User\Wishlist|int $wishlist
     * @param  \App\Models\Item\Item         $item
     * @param  \App\Models\User\User         $user
     * @return bool
     */
    public function createWishlistItem($wishlist, $item, $user)
    {
        DB::beginTransaction();

        try {
            // Perform validation if not being added to default wishlist
            if($wishlist)
                if($wishlist->user_id != $user->id) throw new \Exception('This wishlist does not belong to you.');

            // Create wishlist item
            WishlistItem::create([
                'wishlist_id' => $wishlist ? $wishlist->id : 0,
                'user_id' => $wishlist ? null : $user->id,
                'item_id' => $item->id,
                'count' => 1
            ]);

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Updates an item in a wishlist.
     *
     * @param  \App\Models\User\Wishlist|int $wishlist
     * @param  \App\Models\Item\Item         $item
     * @param  \App\Models\User\User         $user
     * @return bool
     */
    public function updateWishlistItem($wishlist, $item, $data, $user)
    {
        DB::beginTransaction();

        try {
            // Perform validation if not being added to default wishlist
            if($wishlist)
                if($wishlist->user_id != $user->id) throw new \Exception('This wishlist does not belong to you.');

            // Find wishlist item
            $wishlistItem = WishlistItem::where('item_id', $item->id)->where('wishlist_id', $wishlist ? $wishlist->id : $wishlist);
            if(!$wishlist)
                $wishlistItem = $wishlistItem->where('user_id', $user->id);
            $wishlistItem = $wishlistItem->first();

            // Double-check that it exists
            if(!$wishlistItem) throw new \Exception('Invalid wishlist item.');

            if(isset($data['count']) && $data['count'] == 0) {
                $wishlistItem->delete();
            }
            else {
                // Check that the maximum would not be exceeded
                if($wishlistItem->count == 9999) throw new \Exception('Cannot add wishlist item as count would exceed 9999.');

                // Update wishlist item
                $wishlistItem->update([
                    'count' => isset($data['count']) ? $data['count'] : $wishlistItem->count += 1
                ]);
            }

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Moves an item between wishlists.
     *
     * @param  \App\Models\User\Wishlist|int $wishlist
     * @param  \App\Models\Item\Item         $item
     * @param  \App\Models\User\User         $user
     * @return bool
     */
    public function moveWishlistItem($wishlist, $item, $data, $user)
    {
        DB::beginTransaction();

        try {
            // Perform validation if not being added to default wishlist
            if($wishlist)
                if($wishlist->user_id != $user->id) throw new \Exception('This wishlist does not belong to you.');

            // As well as validation if not transferring from the default wishlist
            if($data['source_id'] != 0) {
                $source = Wishlist::where('id', $data['source_id'])->first();
                if(!$source) throw new \Exception('Invalid origin wishlist.');
                if($source->user_id != $user->id) throw new \Exception('The origin wishlist does not belong to you.');
            }

            // Find source wishlist item
            $sourceItem = WishlistItem::where('item_id', $item->id)->where('wishlist_id', isset($source) ? $source->id : 0);
            if(!isset($source))
                $sourceItem = $sourceItem->where('user_id', $user->id);
            $sourceItem = $sourceItem->first();

            // Double-check that it exists
            if(!$sourceItem) throw new \Exception('Invalid wishlist item.');

            // Check if there's an existing wishlist item at the destination
            $wishlistItem = WishlistItem::where('item_id', $item->id)->where('wishlist_id', $wishlist ? $wishlist->id : $wishlist);
            if(!$wishlist)
                $wishlistItem = $wishlistItem->where('user_id', $user->id);
            $wishlistItem = $wishlistItem->first();

            // Either edit or create a wishlist item at the destination
            if($wishlistItem) {
                // Check that the maximum would not be exceeded
                if(($wishlistItem->count + $sourceItem->count) > 9999) throw new \Exception('Cannot move wishlist item as count would exceed 9999.');

                // Update destination
                $wishlistItem->update(['count' => $wishlistItem->count += $sourceItem->count]);
            }
            else {
                WishlistItem::create([
                    'wishlist_id' => $wishlist ? $wishlist->id : 0,
                    'user_id' => $wishlist ? null : $user->id,
                    'item_id' => $item->id,
                    'count' => $sourceItem->count
                ]);
            }

            // And remove it from the source wishlist
            $sourceItem->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Delete a wishlist.
     *
     * @param  \App\Models\User\Wishlist $wishlist
     * @param  \App\Models\User\User     $user
     * @return bool
     */
    public function deleteWishlist($wishlist, $user)
    {
        DB::beginTransaction();

        try {
            // Check that the wishlist exists and the user can edit it/it belongs to them
            if(!$wishlist) throw new \Exception('Invalid wishlist.');
            if($wishlist->user_id != $user->id) throw new \Exception('This wishlist does not belong to you.');

            // Delete all items in the wishlist
            $wishlist->items()->delete();

            // Then delete the wishlist itself
            $wishlist->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

}
