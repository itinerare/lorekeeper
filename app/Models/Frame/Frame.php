<?php

namespace App\Models\Frame;

use App\Models\Model;

class Frame extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'parsed_description', 'item_id', 'is_default', 'frame_category_id', 'hash', 'species_id', 'subtype_id'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'frames';

    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'name' => 'required|unique:frames|between:3,100',
        'frame_image' => 'required|mimes:png',
        'back_image' => 'required|mimes:png',
        //'item_id' => 'required'
    ];

    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name' => 'required|between:3,100',
        'frame_image' => 'nullable|mimes:png',
        'back_image' => 'nullable|mimes:png',
        //'item_id' => 'required'
    ];

    /**********************************************************************************************

        SCOPES

    **********************************************************************************************/

    /**
     * Scope a query to sort frames in alphabetical order.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  bool                                   $reverse
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortAlphabetical($query, $reverse = false)
    {
        return $query->orderBy('name', $reverse ? 'DESC' : 'ASC');
    }

    /**
     * Scope a query to sort frames in category order.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortCategory($query)
    {
        if(FrameCategory::all()->count()) return $query->orderBy(FrameCategory::select('sort')->whereColumn('frames.frame_category_id', 'frame_categories.id'), 'DESC');
        return $query;
    }

    /**
     * Scope a query to sort frames by newest first.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortNewest($query)
    {
        return $query->orderBy('id', 'DESC');
    }

    /**
     * Scope a query to sort frames oldest first.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortOldest($query)
    {
        return $query->orderBy('id');
    }

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the category the frame belongs to.
     */
    public function category()
    {
        return $this->belongsTo('App\Models\Frame\FrameCategory', 'frame_category_id');
    }

    /**
     * Get the item the frame belongs to.
     */
    public function item()
    {
        return $this->belongsTo('App\Models\Item\Item');
    }

    /**
     * Get the species the frame belongs to.
     */
    public function species()
    {
        return $this->belongsTo('App\Models\Species\Species');
    }

    /**
     * Get the subtype the frame belongs to.
     */
    public function subtype()
    {
        return $this->belongsTo('App\Models\Species\Subtype');
    }

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
        return '<a href="'.$this->url.'" class="display-item">'.$this->name.'</a>';
    }

    /**
     * Gets the file directory containing the model's image.
     *
     * @return string
     */
    public function getImageDirectoryAttribute()
    {
        return 'images/data/frames';
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
     * Gets the file name of the frame image.
     *
     * @return string
     */
    public function getFrameFileNameAttribute()
    {
        return $this->id . '-' . $this->hash . '-frame.png';
    }

    /**
     * Gets the file name of the back image.
     *
     * @return string
     */
    public function getBackFileNameAttribute()
    {
        return $this->id . '-' . $this->hash . '-back.png';
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
        return asset($this->imageDirectory . '/' . $this->imageFileName);
    }

    /**
     * Gets the URL of the model's image.
     *
     * @return string
     */
    public function getFrameUrlAttribute()
    {
        return asset($this->imageDirectory . '/' . $this->frameFileName);
    }

    /**
     * Gets the URL of the model's image.
     *
     * @return string
     */
    public function getBackUrlAttribute()
    {
        return asset($this->imageDirectory . '/' . $this->backFileName);
    }

    /**
     * Gets the URL of the model's encyclopedia page.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return url('world/frames?name='.$this->name);
    }

}
