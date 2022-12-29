<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;

use App\Models\Feature\FeatureCategory;
use App\Models\Feature\Feature;
use App\Models\Species\Species;
use App\Models\Species\Subtype;

class FeatureService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Feature Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of feature categories and features.
    |
    */

    /**********************************************************************************************

        FEATURE CATEGORIES

    **********************************************************************************************/

    /**
     * Create a category.
     *
     * @param  array                 $data
     * @param  \App\Models\User\User $user
     * @return \App\Models\Feature\FeatureCategory|bool
     */
    public function createFeatureCategory($data, $user)
    {
        DB::beginTransaction();

        try {
            $data = $this->populateCategoryData($data);

            $image = null;
            if(isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }
            else $data['has_image'] = 0;

            $category = FeatureCategory::create($data);

            if ($image) $this->handleImage($image, $category->categoryImagePath, $category->categoryImageFileName);

            return $this->commitReturn($category);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Update a category.
     *
     * @param  \App\Models\Feature\FeatureCategory  $category
     * @param  array                                $data
     * @param  \App\Models\User\User                $user
     * @return \App\Models\Feature\FeatureCategory|bool
     */
    public function updateFeatureCategory($category, $data, $user)
    {
        DB::beginTransaction();

        try {
            // More specific validation
            if(FeatureCategory::where('name', $data['name'])->where('id', '!=', $category->id)->exists()) throw new \Exception("The name has already been taken.");

            $data = $this->populateCategoryData($data, $category);

            $image = null;
            if(isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }

            $category->update($data);

            if ($category) $this->handleImage($image, $category->categoryImagePath, $category->categoryImageFileName);

            return $this->commitReturn($category);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Handle category data.
     *
     * @param  array                                     $data
     * @param  \App\Models\Feature\FeatureCategory|null  $category
     * @return array
     */
    private function populateCategoryData($data, $category = null)
    {
        if(isset($data['description']) && $data['description']) $data['parsed_description'] = parse($data['description']);

        if(isset($data['remove_image']))
        {
            if($category && $category->has_image && $data['remove_image'])
            {
                $data['has_image'] = 0;
                $this->deleteImage($category->categoryImagePath, $category->categoryImageFileName);
            }
            unset($data['remove_image']);
        }

        return $data;
    }

    /**
     * Delete a category.
     *
     * @param  \App\Models\Feature\FeatureCategory  $category
     * @return bool
     */
    public function deleteFeatureCategory($category)
    {
        DB::beginTransaction();

        try {
            // Check first if the category is currently in use
            if(Feature::where('feature_category_id', $category->id)->exists()) throw new \Exception("A trait with this category exists. Please change its category first.");

            if($category->has_image) $this->deleteImage($category->categoryImagePath, $category->categoryImageFileName);
            $category->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Sorts category order.
     *
     * @param  array  $data
     * @return bool
     */
    public function sortFeatureCategory($data)
    {
        DB::beginTransaction();

        try {
            // explode the sort array and reverse it since the order is inverted
            $sort = array_reverse(explode(',', $data));

            foreach($sort as $key => $s) {
                FeatureCategory::where('id', $s)->update(['sort' => $key]);
            }

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }


    /**********************************************************************************************

        FEATURES

    **********************************************************************************************/

    /**
     * Creates a new feature.
     *
     * @param  array                        $data
     * @param  \App\Models\User\User        $user
     * @param  \App\Models\Feature\Feature  $parent
     * @return bool|\App\Models\Feature\Feature
     */
    public function createFeature($data, $user, $parent = null)
    {
        DB::beginTransaction();

        try {
            if(isset($data['feature_category_id']) && $data['feature_category_id'] == 'none') $data['feature_category_id'] = null;
            if(isset($data['species_id']) && $data['species_id'] == 'none') $data['species_id'] = null;
            if(isset($data['subtype_id']) && $data['subtype_id'] == 'none') $data['subtype_id'] = null;

            if((isset($data['feature_category_id']) && $data['feature_category_id']) && !FeatureCategory::where('id', $data['feature_category_id'])->exists()) throw new \Exception("The selected trait category is invalid.");
            if((isset($data['species_id']) && $data['species_id']) && !Species::where('id', $data['species_id'])->exists()) throw new \Exception("The selected species is invalid.");
            if(isset($data['subtype_id']) && $data['subtype_id'])
            {
                $subtype = Subtype::find($data['subtype_id']);
                if(!(isset($data['species_id']) && $data['species_id'])) throw new \Exception('Species must be selected to select a subtype.');
                if(!$subtype || $subtype->species_id != $data['species_id']) throw new \Exception('Selected subtype invalid or does not match species.');
            }

            // An alt type of a feature should not have the same name as an unrelated feature
            if($parent && Feature::where('name', $data['name'])
            ->where('id', '!=', $parent->id)
            ->whereNotIn('id', $parent->altTypes()->pluck('id')->toArray())
            ->exists() ||
            // An alt type of a feature should not have the same name
            // as a feature with the same rarity and species
            Feature::where('name', $data['name'])->where(function($query) use ($data) {
                return $query->where('rarity_id', $data['rarity_id'])
                ->where('species_id', $data['species_id']);
            })->exists())
                throw new \Exception("The name has already been taken.");

            $data = $this->populateData($data);

            $image = null;
            if(isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }
            else $data['has_image'] = 0;

            $feature = Feature::create($data);

            if ($image) $this->handleImage($image, $feature->imagePath, $feature->imageFileName);

            return $this->commitReturn($feature);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Updates a feature.
     *
     * @param  \App\Models\Feature\Feature  $feature
     * @param  array                        $data
     * @param  \App\Models\User\User        $user
     * @param  \App\Models\Feature\Feature  $parent
     * @return bool|\App\Models\Feature\Feature
     */
    public function updateFeature($feature, $data, $user, $parent = null)
    {
        DB::beginTransaction();

        try {
            if(isset($data['feature_category_id']) && $data['feature_category_id'] == 'none') $data['feature_category_id'] = null;
            if(isset($data['species_id']) && $data['species_id'] == 'none') $data['species_id'] = null;
            if(isset($data['subtype_id']) && $data['subtype_id'] == 'none') $data['subtype_id'] = null;

            // More specific validation
            if(
                // Two completely separate features should not have the same name
                // But alt types of this feature should be able
                (!$parent && Feature::where('name', $data['name'])->where('id', '!=', $feature->id)->where('parent_id', '!=', $feature->id)->exists()) ||
                // An alt type of a feature should not have the same name as an unrelated feature
                ($parent && Feature::where('name', $data['name'])
                ->where('id', '!=', $feature->id)
                ->where('id', '!=', $parent->id)
                ->whereNotIn('id', $parent->altTypes()->pluck('id')->toArray())
                ->exists()) ||
                // An alt type of a feature should not have the same name
                // as a feature with the same rarity or species
                ($parent && Feature::where('name', $data['name'])
                ->where('id', '!=', $feature->id)
                ->whereNotIn('id', $parent->altTypes()->pluck('id')->toArray())
                ->where(function($query) use ($data) {
                    return $query->where('rarity_id', $data['rarity_id'])
                    ->where('species_id', $data['species_id']);
                })->exists())
            ) throw new \Exception("The name has already been taken.");

            if((isset($data['feature_category_id']) && $data['feature_category_id']) && !FeatureCategory::where('id', $data['feature_category_id'])->exists()) throw new \Exception("The selected trait category is invalid.");
            if((isset($data['species_id']) && $data['species_id']) && !Species::where('id', $data['species_id'])->exists()) throw new \Exception("The selected species is invalid.");
            if(isset($data['subtype_id']) && $data['subtype_id'])
            {
                $subtype = Subtype::find($data['subtype_id']);
                if(!(isset($data['species_id']) && $data['species_id'])) throw new \Exception('Species must be selected to select a subtype.');
                if(!$subtype || $subtype->species_id != $data['species_id']) throw new \Exception('Selected subtype invalid or does not match species.');
            }

            $data = $this->populateData($data, $feature);

            $image = null;
            if(isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }

            $feature->update($data);

            if ($feature) $this->handleImage($image, $feature->imagePath, $feature->imageFileName);

            // Handle alternate types
            if(isset($data['alt']) && !$parent) {
                foreach($data['alt']['id'] as $key=>$alt) {
                    // Collect data for the alt type
                    $altData[$key] = [
                        'id' => $alt ? $alt : null,
                        'parent_id' => $feature->id,
                        'feature_category_id' => $data['alt']['feature_category_id'][$key],
                        'name' => $data['alt']['name'][$key],
                        'display_mode' => $data['alt']['display_mode'][$key],
                        'rarity_id' => $data['alt']['rarity_id'][$key],
                        'species_id' => $data['alt']['species_id'][$key],
                        'subtype_id' => $data['alt']['subtype_id'][$key],
                        'description' => $alt ? $data['alt']['description'][$key] : null,
                        'display_separate' => $alt ? (isset($data['alt']['display_separate'][$key]) ? 1 : 0) : 1,
                        'image' => isset($data['alt']['image'][$key]) ? $data['alt']['image'][$key] : null,
                        'remove_image' => $alt ? (isset($data['alt']['remove_image'][$key]) ? 1 : 0) : 0,
                    ];

                    // If the ID is already set, modify the existing feature
                    if($alt) {
                        $altFeature = Feature::where('id', $alt)->first();
                        if(!$altFeature) throw new \Exception('Failed to locate alternate type.');

                        if(!$this->updateFeature($altFeature, $altData[$key], $user, $feature))
                            throw new \Exception('Failed to update alternate type.');
                    }
                    // Otherwise create the feature
                    else
                        if(!$this->createFeature($altData[$key], $user, $feature))
                            throw new \Exception('Failed to create alternate type.');
                }

                // Check for removed alt types
                if($feature->altTypes()->whereNotIn('id', $data['alt']['id'])) {
                    foreach($feature->altTypes()->whereNotIn('id', $data['alt']['id'])->get() as $deletedType) {
                        if(!$this->deleteFeature($deletedType))
                            throw new \Exception('Failed to delete removed alternate type.');
                    }
                }
            }
            elseif($feature->altTypes->count() && !$parent) {
                // Remove extant alt types
                foreach($feature->altTypes as $altType)
                    if(!$this->deleteFeature($altType))
                        throw new \Exception('Failed to delete alternate type(s).');
            }

            return $this->commitReturn($feature);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Processes user input for creating/updating a feature.
     *
     * @param  array                        $data
     * @param  \App\Models\Feature\Feature  $feature
     * @return array
     */
    private function populateData($data, $feature = null)
    {
        if(isset($data['description']) && $data['description']) $data['parsed_description'] = parse($data['description']);
        else $data['parsed_description'] = null;
        if(isset($data['species_id']) && $data['species_id'] == 'none') $data['species_id'] = null;
        if(isset($data['feature_category_id']) && $data['feature_category_id'] == 'none') $data['feature_category_id'] = null;
        if(isset($data['remove_image']))
        {
            if($feature && $feature->has_image && $data['remove_image'])
            {
                $data['has_image'] = 0;
                $this->deleteImage($feature->imagePath, $feature->imageFileName);
            }
            unset($data['remove_image']);
        }

        return $data;
    }

    /**
     * Deletes a feature.
     *
     * @param  \App\Models\Feature\Feature  $feature
     * @return bool
     */
    public function deleteFeature($feature)
    {
        DB::beginTransaction();

        try {
            // Check first if the feature is currently in use
            if(DB::table('character_features')->where('feature_id', $feature->id)->exists()) throw new \Exception("A character with this trait exists. Please remove the trait first.");

            if($feature->has_image) $this->deleteImage($feature->imagePath, $feature->imageFileName);
            $feature->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
}
