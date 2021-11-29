<?php

namespace App\Models\Status;

use Config;
use App\Models\Model;

class StatusEffect extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'parsed_description', 'data', 'has_image'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'status_effects';

    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'name' => 'required|unique:currencies|between:3,100',
        'description' => 'nullable',
        'severity_name.*' => 'nullable|required_with:severity_breakpoint.*',
        'severity_breakpoint.*' => 'nullable|required_with:severity_name.*',
    ];

    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name' => 'required|between:3,100',
        'description' => 'nullable',
        'severity_name.*' => 'nullable|required_with:severity_breakpoint.*',
        'severity_breakpoint.*' => 'nullable|required_with:severity_name.*',
    ];

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Displays the model's name, linked to its encyclopedia page.
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return '<a href="'.$this->url.'" class="display-status">'.$this->name.'</a>';
    }

    /**
     * Gets the file directory containing the model's image.
     *
     * @return string
     */
    public function getImageDirectoryAttribute()
    {
        return 'images/data/statuses';
    }

    /**
     * Gets the file name of the model's image.
     *
     * @return string
     */
    public function getImageFileNameAttribute()
    {
        return $this->id . '-image.png';
    }

    /**
     * Gets the path to the file directory containing the model's image.
     *
     * @return string
     */
    public function getImagePathAttribute()
    {
        return public_path($this->imageDirectory);
    }

    /**
     * Gets the URL of the model's image.
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        if (!$this->has_image) return null;
        return asset($this->imageDirectory . '/' . $this->imageFileName);
    }

    /**
     * Gets the URL of the model's encyclopedia page.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return url('world/status-effects?name='.$this->name);
    }

    /**
     * Gets the status effect's asset type for asset management.
     *
     * @return string
     */
    public function getAssetTypeAttribute()
    {
        return 'statuses';
    }

    /**
     * Get the data attribute as an associative array.
     *
     * @return array
     */
    public function getDataAttribute()
    {
        if(!$this->id) return null;
        return json_decode($this->attributes['data'], true);
    }
}
