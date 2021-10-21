<?php

namespace App\Models\Challenge;

use Config;
use DB;
use Arr;
use Settings;
use Carbon\Carbon;
use App\Models\Submission\Submission;

use App\Models\Model;

class UserChallenge extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'challenge_id', 'user_id', 'staff_id', 'status', 'staff_comments', 'data'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_challenges';

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = true;

    /**
     * Validation rules for submission updating.
     *
     * @var array
     */
    public static $updateRules = [
        'prompt_text.*' => 'required_without:prompt_url.*',
        'prompt_url.*' => 'nullable|url|required_without:prompt_text.*'
    ];

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the challenge this log is associated with.
     */
    public function challenge()
    {
        return $this->belongsTo('App\Models\Challenge\Challenge', 'challenge_id');
    }

    /**
     * Get the user who made the log.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User\User', 'user_id');
    }

    /**
     * Get the staff who processed the log.
     */
    public function staff()
    {
        return $this->belongsTo('App\Models\User\User', 'staff_id');
    }

    /**********************************************************************************************

        SCOPES

    **********************************************************************************************/

    /**
     * Scope a query to only include current challenges.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    /**
     * Scope a query to only include submitted+ challenges.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOld($query)
    {
        return $query->whereIn('status', ['Old']);
    }

    /**
     * Scope a query to only include a given user's logs.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUser($query)
    {
        return $query->where('user_id', Auth::user()->id);
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
     * Get the viewing URL of the challenge.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return url('challenges/view/'.$this->id);
    }

    /**
     * Get the admin URL (for processing purposes) of the challenge.
     *
     * @return string
     */
    public function getAdminUrlAttribute()
    {
        return url('admin/challenges/edit/'.$this->id);
    }

    /**
     * Get if the challenge is complete.
     *
     * @return string
     */
    public function getIsCompleteAttribute()
    {
        if($this->isOld) return true;
        foreach($this->challenge->data as $key=>$prompt) {
            if(!isset($this->data[$key])) return false;
        }
        return true;
    }

    /**
     * Get if the challenge is old/archival display should be enabled.
     *
     * @return string
     */
    public function getIsOldAttribute()
    {
        if($this->status == 'Old') return true;
        else return false;
    }

    /**
     * Get the submission this challenge is attatched to.
     */
    public function getSubmissionAttribute()
    {
        return Submission::where('url', $this->url)->where('status', 'Approved')->first();
    }

}
