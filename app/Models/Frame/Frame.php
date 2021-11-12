<?php

namespace App\Models\Frame;

use Config;
use App\Models\Model;

class Frame extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'parsed_description', 'is_default', 'frame_category_id', 'hash', 'species_id', 'subtype_id'
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
        'species_id' => 'nullable|required_with:subtype_id',
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
        'species_id' => 'nullable|required_with:subtype_id',
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

    /**
     * Gets the context-sensitive width of the frame's background.
     *
     * @return int
     */
    public function getContextWidthAttribute()
    {
        if(isset($this->species_id)) {
            if(isset($this->subtype_id) && null !== Config::get('lorekeeper.settings.frame_dimensions.'.$this->species_id.'.'.$this->subtype_id.'.width')) return Config::get('lorekeeper.settings.frame_dimensions.'.$this->species_id.'.'.$this->subtype_id.'.width');
            if(null !== Config::get('lorekeeper.settings.frame_dimensions.'.$this->species_id.'.width')) return Config::get('lorekeeper.settings.frame_dimensions.'.$this->species_id.'.width');
        }
        return Config::get('lorekeeper.settings.frame_dimensions.width');
    }

    /**
     * Gets the context-sensitive height of the frame's background.
     *
     * @return int
     */
    public function getContextHeightAttribute()
    {
        if(isset($this->species_id)) {
            if(isset($this->subtype_id) && null !== Config::get('lorekeeper.settings.frame_dimensions.'.$this->species_id.'.'.$this->subtype_id.'.height')) return Config::get('lorekeeper.settings.frame_dimensions.'.$this->species_id.'.'.$this->subtype_id.'.height');
            if(null !== Config::get('lorekeeper.settings.frame_dimensions.'.$this->species_id.'.height')) return Config::get('lorekeeper.settings.frame_dimensions.'.$this->species_id.'.height');
        }
        return Config::get('lorekeeper.settings.frame_dimensions.height');
    }

    /**********************************************************************************************

        OTHER FUNCTIONS

    **********************************************************************************************/

    /**
     * Checks if a frame is valid for a given species and/or subtype combination.
     *
     * @param  int                      $species_id
     * @param  int                      $subtype_id
     * @return bool
     */
    public function isValid($species_id = null, $subtype_id = null)
    {
        // First fetch all available frames
        $availableFrames = $this->availableFrames($species_id, $subtype_id)->pluck('name', 'id');

        // Then check if the provided frame ID is present
        if(isset($availableFrames[$this->id])) return 1;
        return 0;
    }

    /**
     * Gets the context-sensitive available frames.
     * This checks against the frame sizes configured for the site.
     *
     * @param  int              $species_id
     * @param  int              $subtype_id
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function availableFrames($species_id = null, $subtype_id = null)
    {
        // First filter down to species with configured settings
        $configuredSpecies = collect(Config::get('lorekeeper.settings.frame_dimensions'))->filter(function($setting, $key) {
            return is_numeric($key);
        });

        // Size is configured first by species and then by subtype ID,
        // so start by checking that species-specific settings exist at all
        if((isset($species_id) && $species_id) && isset($configuredSpecies[$species_id])) {
            // Get just the settings for the specified species for convenience
            $speciesSettings = $configuredSpecies[$species_id];

            // Subtypes are more restrictive, so then check that first
            if(isset($subtype_id) && $subtype_id && isset($speciesSettings[$subtype_id])) {
                // If there are subtype-specific settings, that means only
                // frames specifically for that subtype are "safe"
                return $this->where('subtype_id', $subtype_id)->get();
            }
            else {
                // If this is of a configured species, but not a configured subtype,
                // it's important to return only frames for the species but not any
                // configured subtypes
                // To this end, filter the species settings down to just subtypes
                $configuredSubtypes = collect($speciesSettings)->filter(function($setting, $key) {
                    return is_numeric($key);
                });

                // Then return all frames specific to this species except those for
                // subtypes with configured dimensions
                return $this->where('species_id', $species_id)->where(function($query) use($configuredSubtypes) {
                    return $query->whereNull('subtype_id')
                    ->orWhereNotIn('subtype_id', $configuredSubtypes->keys());
                })->get();
            }
        }
        // Otherwise return all frames that do not correspond to a configured species
        else return $this->where(function($query) use($configuredSpecies) {
            return $query->whereNull('species_id')
            ->orWhereNotIn('species_id', $configuredSpecies->keys());
        })->get();
    }

    /**
     * Gets the context-sensitive default frame.
     *
     * @param  int              $species_id
     * @param  int              $subtype_id
     * @return \App\Models\Frame\Frame
     */
    public function defaultFrame($species_id = null, $subtype_id = null)
    {
        // First fetch all available frames
        $availableFrames = $this->availableFrames($species_id, $subtype_id);

        // Then locate and return the default frame from among them
        return $availableFrames->where('is_default', 1)->first();
    }
}
