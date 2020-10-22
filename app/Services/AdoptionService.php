<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;

use App\Models\Adoption\Adoption;
use App\Models\Adoption\AdoptionStock;

class AdoptionService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Adoption Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of adoptions and adoption stock.
    |
    */

    /**********************************************************************************************
     
        ADOPTIONS

    **********************************************************************************************/
    
    /**
     * Creates a new adoption.
     *
     * @param  array                  $data 
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\Adoption\Adoption
     */
    public function createAdoption($data, $user)
    {
        DB::beginTransaction();

        try {

            $data = $this->populateAdoptionData($data);

            $image = null;
            if(isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }
            else $data['has_image'] = 0;

            $adoption = Adoption::create($data);

            if ($image) $this->handleImage($image, $adoption->adoptionImagePath, $adoption->adoptionImageFileName);

            return $this->commitReturn($adoption);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    /**
     * Updates a adoption.
     *
     * @param  \App\Models\Adoption\Adoption  $adoption
     * @param  array                  $data 
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\Adoption\Adoption
     */
    public function updateAdoption($adoption, $data, $user)
    {
        DB::beginTransaction();

        try {
            // More specific validation
            if(Adoption::where('name', $data['name'])->where('id', '!=', $adoption->id)->exists()) throw new \Exception("The name has already been taken.");

            $data = $this->populateAdoptionData($data, $adoption);

            $image = null;            
            if(isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }

            $adoption->update($data);

            if ($adoption) $this->handleImage($image, $adoption->adoptionImagePath, $adoption->adoptionImageFileName);

            return $this->commitReturn($adoption);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    /**
     * Updates adoption stock.
     *
     * @param  \App\Models\Adoption\Adoption  $adoption
     * @param  array                  $data 
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\Adoption\Adoption
     */
    public function updateAdoptionStock($adoption, $data, $user)
    {
        DB::beginTransaction();

        try {
            foreach($data['character_id'] as $key => $characterId)
            {
                if(!$data['cost'][$key]) throw new \Exception("One or more of the characters is missing a cost.");
            }

            // Clear the existing adoption stock
            $adoption->stock()->delete();

            foreach($data['character_id'] as $key => $characterId)
            {
                $adoption->stock()->create([
                    'adoption_id'               => $adoption->id,
                    'character_id'               => $data['character_id'][$key],
                    'currency_id'           => $data['currency_id'][$key],
                    'cost'                  => $data['cost'][$key],
                    'use_user_bank'         => isset($data['use_user_bank'][$key]),
                    'use_character_bank'    => isset($data['use_character_bank'][$key]),
                    'purchase_limit'        => $data['purchase_limit'][$key],
                ]);
            }

            return $this->commitReturn($adoption);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Processes user input for creating/updating a adoption.
     *
     * @param  array                  $data 
     * @param  \App\Models\Adoption\Adoption  $adoption
     * @return array
     */
    private function populateAdoptionData($data, $adoption = null)
    {
        if(isset($data['description']) && $data['description']) $data['parsed_description'] = parse($data['description']);
        $data['is_active'] = isset($data['is_active']);
        
        if(isset($data['remove_image']))
        {
            if($adoption && $adoption->has_image && $data['remove_image']) 
            { 
                $data['has_image'] = 0; 
                $this->deleteImage($adoption->adoptionImagePath, $adoption->adoptionImageFileName); 
            }
            unset($data['remove_image']);
        }

        return $data;
    }
    
    /**
     * Deletes a adoption.
     *
     * @param  \App\Models\Adoption\Adoption  $adoption
     * @return bool
     */
    public function deleteAdoption($adoption)
    {
        DB::beginTransaction();

        try {
            // Delete adoption stock
            $adoption->stock()->delete();

            if($adoption->has_image) $this->deleteImage($adoption->adoptionImagePath, $adoption->adoptionImageFileName); 
            $adoption->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Sorts adoption order.
     *
     * @param  array  $data
     * @return bool
     */
    public function sortAdoption($data)
    {
        DB::beginTransaction();

        try {
            // explode the sort array and reverse it since the order is inverted
            $sort = array_reverse(explode(',', $data));

            foreach($sort as $key => $s) {
                Adoption::where('id', $s)->update(['sort' => $key]);
            }

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
}