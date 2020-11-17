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
        'status', 'worth', 'currency_id'
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
     * Validation rules for surrender creation.
     *
     * @var array
     */
    public static $createRules = [
        'character_id' => 'required',
    ];
    
    /**
     * Validation rules for surrender updating.
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
     * Scope a query to sort surrenders oldest first.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortOldest($query)
    {
        return $query->orderBy('id');
    }

    /**
     * Scope a query to sort surrenders by newest first.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortNewest($query)
    {
        return $query->orderBy('id', 'DESC');
    }

     /**
     * Scope a query to only include pending surrenders.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Pending');
    }

    /**
     * Scope a query to only include viewable surrenders.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeViewable($query, $user)
    {
        if($user && $user->hasPower('manage_surrenders')) return $query;
        return $query->where(function($query) use ($user) {
            if($user) $query->where('user_id', $user->id)->orWhere('status', 'Approved');
            else $query->where('status', 'Approved');
        });
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
     * Get the user who made the surrender.
     */
    public function user() 
    {
        return $this->belongsTo('App\Models\User\User', 'user_id');
    }
    
    /**
     * Get the staff who processed the surrender.
     */
    public function staff() 
    {
        return $this->belongsTo('App\Models\User\User', 'staff_id');
    }

    /**********************************************************************************************
    
        ACCESSORS

    **********************************************************************************************/

    /**
     * Get the viewing URL of the surrender/claim.
     *
     * @return string
     */
    public function getViewUrlAttribute()
    {
        return url('surrender/view/'.$this->id);
    }

    /**
     * Get the admin URL (for processing purposes) of the surrender/claim.
     *
     * @return string
     */
    public function getAdminUrlAttribute()
    {
        return url('admin/surrenders/edit/'.$this->id);
    }
}
