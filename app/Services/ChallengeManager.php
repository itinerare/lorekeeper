<?php namespace App\Services;

use App\Services\Service;

use DB;
use Auth;
use Config;
use Settings;

use App\Models\User\User;
use App\Models\Challenge\Challenge;
use App\Models\Challenge\UserChallenge;

class ChallengeManager extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Bingo Manager
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of bingo challenges.
    |
    */

    /**
     * Registers a new challenge.
     *
     * @param  \App\Models\Challenge\Challenge  $challenge
     * @param  \App\Models\User\User            $user
     * @return mixed
     */
    public function createChallengeLog($challenge, $user)
    {
        DB::beginTransaction();

        try {
            // Check that challenges can be registered for
            if(Settings::get('challenges_concurrent') < 1) throw new \Exception('Challenge registration is currently closed.');
            // Verify that the selected challenge is valid
            if(!$challenge) throw new \Exception('Invalid challenge selected.');
            // Verify that the user can register for a new challenge presently
            if(!$user->canChallenge) throw new \Exception('You cannot register for a new challenge at this time.');

            // Create the log
            $log = UserChallenge::create([
                'user_id' => $user->id,
                'status' => 'Active',
                'challenge_id' => $challenge->id
            ]);

            return $this->commitReturn($log);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Edits a challenge.
     *
     * @param  array                                $data
     * @param  \App\Models\Challenge\UserChallenge  $challenge
     * @param  \App\Models\User\User                $user
     * @return mixed
     */
    public function editChallenge($data, $challenge, $user)
    {
        DB::beginTransaction();

        try {
            // 1. check that the challenge exists
            // 2. check that the challenge is not old/can be interacted with
            if(!$challenge) throw new \Exception("Invalid challenge.");
            if($challenge->isOld) throw new \Exception("This challenge is old.");

            // Fetch any existing data
            $data['data'] = $challenge->data;

            // Update the data for only the submitted prompt
            foreach($data['prompt_url'] as $key=>$url) {
                $data['data'][$key]['url'] = $url;
                $data['data'][$key]['text'] = $data['prompt_text'][$key];
            }

            // JSON encode the data
            $data['data'] = json_encode($data['data']);

            // Update the challenge
            $challenge->update(['data' => $data['data']]);

            return $this->commitReturn($challenge);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Accepts a challenge.
     *
     * @param  array                                $data
     * @param  \App\Models\Challenge\UserChallenge  $challenge
     * @param  \App\Models\User\User                $user
     * @return mixed
     */
    public function acceptChallenge($data, $challenge, $user)
    {
        DB::beginTransaction();

        try {
            // 1. check that the challenge exists
            // 2. check that the challenge is not old/can be interacted with
            if(!$challenge) throw new \Exception("Invalid challenge.");
            if($challenge->isOld) throw new \Exception("This challenge is old.");

            // The only things we need to set are:
            // 1. staff comments
            // 2. staff ID
            // 3. status
            $challenge->update([
                'staff_comments' => $data['staff_comments'],
                'staff_id' => $user->id,
                'status' => 'Old'
            ]);

            return $this->commitReturn($challenge);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
}
