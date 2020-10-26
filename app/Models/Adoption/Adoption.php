<?php

namespace App\Models\Adoption;

use Config;
use App\Models\Model;

class Adoption extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'has_image', 'description', 'parsed_description', 'is_active'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'adoptions';
    
    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'name' => 'required|unique:item_categories|between:3,25',
        'description' => 'nullable',
        'image' => 'mimes:png',
    ];
    
    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name' => 'required|between:3,25',
        'description' => 'nullable',
        'image' => 'mimes:png',
    ];

    /**********************************************************************************************
    
        RELATIONS

    **********************************************************************************************/

    /**
     * Get the adoption stock.
     */
    public function stock() 
    {
        return $this->hasMany('App\Models\Adoption\AdoptionStock');
    }
    
    /**
     * Get the adoption stock as items for display purposes.
     */
    public function displayStock()
    {
        return $this->belongsToMany('App\Models\Character\Character', 'adoption_stock')->withPivot('character_id', 'currency_id', 'cost', 'use_user_bank', 'use_character_bank', 'is_limited_stock', 'quantity', 'purchase_limit', 'id');
    }

    /**********************************************************************************************
    
        ACCESSORS

    **********************************************************************************************/
    
    /**
     * Displays the adoption's name, linked to its purchase page.
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return '<a href="'.$this->url.'" class="display-adoption">'.$this->name.'</a>';
    }

    /**
     * Gets the file directory containing the model's image.
     *
     * @return string
     */
    public function getImageDirectoryAttribute()
    {
        return 'images/data/adoptions';
    }

    /**
     * Gets the file name of the model's image.
     *
     * @return string
     */
    public function getAdoptionImageFileNameAttribute()
    {
        return $this->id . '-image.png';
    }

    /**
     * Gets the path to the file directory containing the model's image.
     *
     * @return string
     */
    public function getAdoptionImagePathAttribute()
    {
        return public_path($this->imageDirectory);
    }
    
    /**
     * Gets the URL of the model's image.
     *
     * @return string
     */
    public function getAdoptionImageUrlAttribute()
    {
        if (!$this->has_image) return null;
        return asset($this->imageDirectory . '/' . $this->adoptionImageFileName);
    }

    /**
     * Gets the URL of the model's encyclopedia page.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return url('adoptions');
    }
}
