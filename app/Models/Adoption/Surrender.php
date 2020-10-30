<?php

namespace App\Models\Adoption;

use Config;
use App\Models\Model;

class Surrender extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'character_id', 'user_id', 'staff_id', 'notes',
        'comments', 'staff_comments',
        'status', 'worth'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'surrenders';

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = true;
    
    /**
     * Validation rules for submission creation.
     *
     * @var array
     */
    public static $createRules = [
        'character_id' => 'required',
    ];
    
    /**
     * Validation rules for submission updating.
     *
     * @var array
     */
    public static $updateRules = [
        'character_id' => 'required',
    ];

    /**********************************************************************************************
    
        SCOPES

    **********************************************************************************************/

    /**
     * Scope a query to sort submissions oldest first.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortOldest($query)
    {
        return $query->orderBy('id');
    }

    /**
     * Scope a query to sort submissions by newest first.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortNewest($query)
    {
        return $query->orderBy('id', 'DESC');
    }

    /**********************************************************************************************
    
        RELATIONS

    **********************************************************************************************/
    
    /**
     * Get the character this surrender is for.
     */
    public function character() 
    {
        return $this->belongsTo('App\Models\Character\Character', 'character_id');
    }
    
    /**
     * Get the user who made the submission.
     */
    public function user() 
    {
        return $this->belongsTo('App\Models\User\User', 'user_id');
    }
    
    /**
     * Get the staff who processed the submission.
     */
    public function staff() 
    {
        return $this->belongsTo('App\Models\User\User', 'staff_id');
    }

    /**********************************************************************************************
    
        ACCESSORS

    **********************************************************************************************/

    /**
     * Get the data attribute as an associative array.
     *
     * @return array
     */
    public function getDataAttribute()
    {
        return json_decode($this->attributes['data'], true);
    }

    /**
     * Get the viewing URL of the submission/claim.
     *
     * @return string
     */
    public function getViewUrlAttribute()
    {
        return url('surrender/view/'.$this->id);
    }

    /**
     * Get the admin URL (for processing purposes) of the submission/claim.
     *
     * @return string
     */
    public function getAdminUrlAttribute()
    {
        return url('admin/surrenders/edit/'.$this->id);
    }
}
