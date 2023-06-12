<?php

namespace App\Models\User;

use App\Models\Model;

class WishlistItem extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'wishlist_id', 'user_id', 'item_id', 'count'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_wishlist_items';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = false;

    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'count' => 'nullable|numeric|max:9999'
    ];

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the wishlist this item belongs to.
     */
    public function wishlist()
    {
        return $this->belongsTo('App\Models\User\UserWishlist', 'wishlist_id');
    }

    /**
     * Get the corresponding item.
     */
    public function item()
    {
        return $this->belongsTo('App\Models\Item\Item');
    }

}
