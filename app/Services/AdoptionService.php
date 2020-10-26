<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;

use App\Models\Adoption\Adoption;
use App\Models\Adoption\AdoptionStock;
use App\Models\Adoption\AdoptionCurrency;

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
            if(!$data['cost']) throw new \Exception("The character is missing a cost.");

            $stock_id = AdoptionStock::count() + 1;

            $this->populateCosts(array_only($data, ['currency_id', 'cost']), $stock_id);

            {
                $adoption->stock()->create([
                    'adoption_id'           => $adoption->id,
                    'character_id'          => $data['character_id'],
                    'use_user_bank'         => isset($data['use_user_bank']),
                    'use_character_bank'    => isset($data['use_character_bank']),
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

    private function populateCosts($data, $stock_id) {
        
        if(isset($data['currency_id'])) {
            foreach($data['currency_id'] as $key => $type)
            {
                AdoptionCurrency::create([
                    'stock_id'       => $stock_id,
                    'currency_id' => $type,
                    'cost'   => $data['cost'][$key],
                ]);
            }
        }

    }
}