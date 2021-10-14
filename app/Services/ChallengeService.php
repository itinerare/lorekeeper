<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;
use Arr;

use App\Models\Challenge\Challenge;
use App\Models\Challenge\UserChallenge;

class ChallengeService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Challenge Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of challenges.
    |
    */

    /**
     * Creates a new challenge.
     *
     * @param  array                  $data
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\Challenge\Challenge
     */
    public function createChallenge($data, $user)
    {
        DB::beginTransaction();

        try {
            $data = $this->populateData($data);

            $challenge = Challenge::create(Arr::only($data, [
                'name', 'description', 'parsed_description', 'rules',
                'is_active', 'data'
            ]));

            return $this->commitReturn($challenge);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Updates a challenge.
     *
     * @param  \App\Models\Challenge\Challenge  $challenge
     * @param  array                            $data
     * @param  \App\Models\User\User            $user
     * @return bool|\App\Models\Challenge\Challenge
     */
    public function updateChallenge($challenge, $data, $user)
    {
        DB::beginTransaction();

        try {
            // More specific validation
            if(Challenge::where('name', $data['name'])->where('id', '!=', $challenge->id)->exists()) throw new \Exception("The name has already been taken.");

            $data = $this->populateData($data, $challenge);

            $challenge->update(Arr::only($data, [
                'name', 'description', 'parsed_description', 'rules',
                'is_active', 'data'
            ]));

            return $this->commitReturn($challenge);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Processes user input for creating/updating a challenge.
     *
     * @param  array                            $data
     * @param  \App\Models\Challenge\Challenge  $challenge
     * @return array
     */
    private function populateData($data, $challenge = null)
    {
        $data['parsed_description'] = parse($data['description']);
        if(!isset($data['is_active'])) $data['is_active'] = 0;

        // Figure out what the new key should be
        if($challenge && isset($challenge->data))
            $newKey = max(array_keys($challenge->data)) + 1;
        else
            $newKey = 0;

        // Process prompts
        if(isset($data['prompt_name']))
        foreach($data['prompt_name'] as $key=>$prompt) {
            // Find or determine the prompt's key
            if(!isset($data['prompt_key'][$key])) {
                $promptKey = $newKey;
                $newKey += 1;
            }
            else
                $promptKey = $data['prompt_key'][$key];

            // Record data with the prompt's key
            $data['data'][$promptKey] = [
                'name' => $data['prompt_name'][$key],
                'description' => $data['prompt_description'][$key]
            ];
        }

        if(isset($data['data'])) $data['data'] = json_encode($data['data']);
        else $data['data'] = null;

        return $data;
    }

    /**
     * Deletes a challenge.
     *
     * @param  \App\Models\Challenge\Challenge  $challenge
     * @return bool
     */
    public function deleteChallenge($challenge)
    {
        DB::beginTransaction();

        try {
            // Check first if the category is currently in use
            if(UserChallenge::where('challenge_id', $challenge->id)->exists()) throw new \Exception("A user challenge log under this challenge exists. Deleting the challenge will break the challenge's log-- consider setting the challenge to be inactive instead.");

            $challenge->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
}
