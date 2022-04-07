<?php

namespace App\Models\Adoption;

use App\Models\Model;

class AdoptionLog extends Model
{
    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = true;

    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'stock_id'    => 'required',
        'adoption_id' => 'required',
        'bank'        => 'required|in:user,character',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'adoption_id', 'character_id', 'user_id', 'currency_id', 'cost', 'adopt_id', 'quantity',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'adoption_log';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the user who purchased the item.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User\User');
    }

    /**
     * Get the character who purchased the item.
     */
    public function character()
    {
        return $this->belongsTo('App\Models\Character\Character');
    }

    /**
     * Get the purchased character.
     */
    public function adopt()
    {
        return $this->belongsTo('App\Models\Character\Character', 'adopt_id');
    }

    /**
     * Get the adoption the item was purchased from.
     */
    public function adoption()
    {
        return $this->belongsTo('App\Models\Adoption\Adoption');
    }

    /**
     * Get the currency used to purchase the item.
     */
    public function currency()
    {
        return $this->belongsTo('App\Models\Currency\Currency');
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Get the item data that will be added to the stack as a record of its source.
     *
     * @return string
     */
    public function getItemDataAttribute()
    {
        return 'Purchased from '.$this->adoption->name.' by '.($this->character_id ? $this->character->slug.' (owned by '.$this->user->name.')' : $this->user->displayName).' for '.$this->cost.' '.$this->currency->name.'.';
    }
}
