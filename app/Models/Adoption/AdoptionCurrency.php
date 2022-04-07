<?php

namespace App\Models\Adoption;

use App\Models\Model;

class AdoptionCurrency extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'stock_id', 'currency_id', 'cost',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'adoption_currency';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the adopt attached to this.
     */
    public function adopt()
    {
        return $this->belongsTo('App\Models\Adoption\AdoptionStock', 'stock_id');
    }

    /**
     * Get the currency attached to this.
     */
    public function currency()
    {
        return $this->belongsTo('App\Models\Currency\Currency', 'currency_id');
    }
}
