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
     * Creates adoption stock.
     *
     * @param  \App\Models\Adoption\Adoption  $adoption
     * @param  array                  $data 
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\Adoption\Adoption
     */
    public function createAdoptionStock($adoption, $data)
    {
        DB::beginTransaction();

        try {

            if(!$data['cost']) throw new \Exception("The character is missing a cost.");

            // Validation
            $data['adoption_id'] = 1;
            if(!isset($data['use_user_bank'])) $data['use_user_bank'] = 0;
            if(!isset($data['use_character_bank'])) $data['use_character_bank'] = 0;
            if(!isset($data['is_visible'])) $data['is_visible'] = 0;

            $stock = AdoptionStock::create(array_only($data, ['adoption_id', 'character_id', 'use_user_bank', 'use_character_bank', 'is_visible']));
            if(AdoptionStock::where('character_id', $data['character_id'])->where('id', '!=', $stock->id)->exists()) throw new \Exception("This character is already in another stock!");

            $this->popCreationCosts(array_only($data, ['currency_id', 'cost']), $stock);

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
    public function updateAdoptionStock($adoption, $data, $id)
    {

        DB::beginTransaction();

        try {
            if(!$data['cost']) throw new \Exception("The character is missing a cost.");
            if(!$data['currency_id']) throw new \Exception("The character is missing a currency type.");
            if(AdoptionStock::where('character_id', $data['character_id'])->where('id', '!=', $id)->exists()) throw new \Exception("This character is already in another stock!");

            if(!isset($data['is_visible'])) $data['is_visible'] = 0;
            
            $this->populateCosts(array_only($data, ['currency_id', 'cost']), $id);

            $stock = AdoptionStock::find($id);

            $stock->adoption_id = 1;
            $stock->character_id = $data['character_id'];
            $stock->use_user_bank = isset($data['use_user_bank']);
            $stock->use_character_bank = isset($data['use_character_bank']);
            $stock->is_visible = $data['is_visible'];
            $stock->save();

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
     * Deletes stock
     *
     * @param  array                  $data 
     * @param  \App\Models\Adoption\Adoption  $adoption
     * @return array
     */
    public function deleteStock($id) {
        
        DB::beginTransaction();

            try {

                if(!$id) throw new \Exception("This stock doesn't exist");

                $adoptionStock = AdoptionStock::find($id);

                $adoptionStock->delete();
                
                AdoptionCurrency::where('stock_id', $id)->delete();

                return $this->commitReturn($id);

            } catch(\Exception $e) { 
                $this->setError('error', $e->getMessage());
        }
            return $this->rollbackReturn(false);
    }

    /**
     * Processes currencies for use to buy
     *
     * @param  array                  $data 
     * @param  \App\Models\Adoption\Adoption  $adoption
     * @return array
     */
    private function populateCosts($data, $id) {

        $stocks = AdoptionStock::find($id);
        // Delete existing currencies to prevent overlaps etc
        $stocks->currency()->delete();

        $currency = array_unique($data['currency_id']);
        if(isset($currency)) {
            foreach($currency as $key => $type)
            {
                AdoptionCurrency::create([
                    'stock_id'       => $id,
                    'currency_id' => $type,
                    'cost'   => $data['cost'][$key],
                ]);
            }
        }

    }

    /**
     * Processes currencies for use to buy
     *
     * @param  array                  $data 
     * @param  \App\Models\Adoption\Adoption  $adoption
     * @return array
     */
    private function popCreationCosts($data, $id) {

        if($data['currency_id'] == array()) {
        $currency = array_unique($data['currency_id']);
        foreach($currency as $key => $type)
                AdoptionCurrency::create([
                    'stock_id'       => $id->id,
                    'currency_id' => $type,
                    'cost'   => $data['cost'][$key],
                ]);
            }
        
        else {
            $currency = $data['currency_id'];
            AdoptionCurrency::create([
                'stock_id'       => $id->id,
                'currency_id' => $data['currency_id'],
                'cost'   => $data['cost'],
            ]);
        }
    }
            
}