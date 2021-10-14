<?php

namespace App\Http\Controllers\Admin\Data;

use Illuminate\Http\Request;

use Auth;

use App\Models\Challenge\Challenge;
use App\Services\ChallengeService;

use App\Http\Controllers\Controller;

class ChallengeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Admin / Challenge Controller
    |--------------------------------------------------------------------------
    |
    | Handles creation/editing of challenges.
    |
    */

    /**
     * Shows the challenge index.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getChallengeIndex(Request $request)
    {
        return view('admin.challenges.index', [
            'challenges' => Challenge::query()->paginate(20)->appends($request->query())
        ]);
    }

    /**
     * Shows the create challenge page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateChallenge()
    {
        return view('admin.challenges.create_edit_challenge', [
            'challenge' => new Challenge
        ]);
    }

    /**
     * Shows the edit challenge page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditChallenge($id)
    {
        $challenge = Challenge::find($id);
        if(!$challenge) abort(404);

        return view('admin.challenges.create_edit_challenge', [
            'challenge' => $challenge
        ]);
    }

    /**
     * Creates or edits a challenge.
     *
     * @param  \Illuminate\Http\Request       $request
     * @param  App\Services\ChallengeService  $service
     * @param  int|null                       $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditChallenge(Request $request, ChallengeService $service, $id = null)
    {
        $id ? $request->validate(Challenge::$updateRules) : $request->validate(Challenge::$createRules);
        $data = $request->only([
            'name', 'description', 'rules', 'is_active',
            'prompt_name', 'prompt_description', 'prompt_key'
        ]);

        if($id && $service->updateChallenge(Challenge::find($id), $data, Auth::user())) {
            flash('Challenge updated successfully.')->success();
        }
        else if (!$id && $challenge = $service->createChallenge($data, Auth::user())) {
            flash('Challenge created successfully.')->success();
            return redirect()->to('admin/data/challenges/edit/'.$challenge->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Gets the challenge deletion modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteChallenge($id)
    {
        $challenge = Challenge::find($id);
        return view('admin.challenges._delete_challenge', [
            'challenge' => $challenge,
        ]);
    }

    /**
     * Deletes a challenge.
     *
     * @param  \Illuminate\Http\Request       $request
     * @param  App\Services\ChallengeService  $service
     * @param  int                            $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteChallenge(Request $request, ChallengeService $service, $id)
    {
        if($id && $service->deleteChallenge(Challenge::find($id))) {
            flash('Challenge deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/data/challenges');
    }
}
