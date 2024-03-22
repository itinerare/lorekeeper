<?php

namespace App\Models\Shop;

use App\Models\Item\ItemLog;
use App\Models\Model;
use Carbon\Carbon;
use Config;

class UserItemDonation extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'stack_id', 'item_id', 'stock'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_item_donations';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the donated stack.
     */
    public function stack()
    {
        return $this->belongsTo('App\Models\User\UserItem');
    }

    /**
     * Get the item of the donated stack.
     */
    public function item()
    {
        return $this->belongsTo('App\Models\Item\Item');
    }

    /**********************************************************************************************

        SCOPES

    **********************************************************************************************/

    /**
     * Scope a query to only include donated items with a non-zero quantity.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAvailable($query)
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * Scope a query to only include "expired" donated items.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExpired($query)
    {
        if(Config::get('lorekeeper.settings.donation_shop.expiry')) {
            $expiredLogs = ItemLog::where('log_type', 'Donated by User')->where('created_at', '<', Carbon::now()->subMonths(Config::get('lorekeeper.settings.donation_shop.expiry')));

            return $query->where('stock', '>', 0)->whereIn('stack_id', $expiredLogs->pluck('stack_id')->toArray());
        }
        else return collect([]);
    }

    /**********************************************************************************************

        OTHER FUNCTIONS

    **********************************************************************************************/

    /**
     * Get the shop stock as items for display purposes.
     */
    public function displayStock()
    {
        return $this->available()->leftJoin('items', 'user_item_donations.item_id', '=', 'items.id')->select(['user_item_donations.*', 'items.item_category_id']);
    }

}
