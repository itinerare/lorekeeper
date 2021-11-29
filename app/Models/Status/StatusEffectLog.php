<?php

namespace App\Models\Currency;

use Config;
use App\Models\Model;

class CurrencyLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sender_id', 'recipient_id',
        'log', 'log_type', 'data',
        'status_effect_id', 'quantity'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'status_effects_log';

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
     * Get the user who initiated the logged action.
     */
    public function sender()
    {
        $this->belongsTo('App\Models\User\User', 'sender_id');
    }

    /**
     * Get the character who received the logged action.
     */
    public function recipient()
    {
        return $this->belongsTo('App\Models\Character\Character', 'recipient_id');
    }

    /**
     * Get the status effect that is the target of the action.
     */
    public function status()
    {
        return $this->belongsTo('App\Models\Status\StatusEffect');
    }

}
