<?php

namespace App\Models\Adoption;

use App\Models\Model;

class AdoptionStock extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'adoption_id', 'character_id', 'currency_id', 'cost', 'use_user_bank', 'use_character_bank', 'is_limited_stock', 'quantity', 'sort', 'purchase_limit'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'adoption_stock';

    /**********************************************************************************************
    
        RELATIONS

    **********************************************************************************************/
    
    /**
     * Get the character being stocked.
     */
    public function character() 
    {
        return $this->belongsTo('App\Models\Character\Character');
    }
    
    /**
     * Get the adoption that holds this character.
     */
    public function adoption() 
    {
        return $this->belongsTo('App\Models\Adoption\Adoption');
    }
    
    /**
     * Get the currency the character must be purchased with.
     */
    public function currency() 
    {
        return $this->belongsTo('App\Models\Currency\Currency');
    }
}
