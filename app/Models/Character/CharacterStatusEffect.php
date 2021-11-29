<?php

namespace App\Models\Character;

use App\Models\Model;

class CharacterStatusEffect extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'quantity', 'character_id', 'status_effect_id'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'character_status_effects';

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = true;

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the character the record belongs to.
     */
    public function character()
    {
        return $this->belongsTo('App\Models\Character\Character');
    }

    /**
     * Get the status effect associated with this record.
     */
    public function status()
    {
        return $this->belongsTo('App\Models\Status\StatusEffect');
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Get the name of the status effect formatted with the quantity owned.
     *
     * @return string
     */
    public function getNameWithQuantityAttribute()
    {
        return $this->status->name . ' [Severity: ' . $this->quantity . ']';
    }
}
