<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use Auth;
use Carbon\Carbon;
use Settings;
use App\Models\User\User;
use App\Models\Challenge\Challenge;
use App\Models\Challenge\UserChallenge;

use App\Services\ChallengeManager;

use App\Http\Controllers\Controller;

class ChallengeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Challenges Controller
    |--------------------------------------------------------------------------
    |
    | Handles public-facing display of challenges.
    |
    */

    /**
     * Shows the index page.
     *
     * @param  \Illuminate\Http\Request        $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex(Request $request)
    {
        $query = Challenge::active();
        if($request->only(['sort']))
        {
            switch($request->only(['sort'])) {
                case 'name':
                    $query->orderBy('name', 'ASC');
                    break;
                case 'name-reverse':
                    $query->orderBy('name', 'DESC');
                    break;
                case 'newest':
                    $query->orderBy('id', 'DESC');
                    break;
                case 'oldest':
                    $query->orderBy('id', 'ASC');
                    break;
            }
        }
        else $query->orderBy('name', 'ASC');

        return view('challenges.index', [
            'challenges' => $query->paginate(20)->appends($request->query()),
        ]);
    }

    /**
     * Views a challenge by ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getChallenge(Request $request, $id)
    {
        $challenge = Challenge::active()->where('id', $id)->first();
        if(!$challenge) abort(404);

        return view('challenges.challenge', [
            'challenge' => $challenge
        ]);
    }

    /**
     * Gets the challenge registration modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getRegister($id)
    {
        $challenge = Challenge::active()->where('id', $id)->first();
        return view('challenges._register', [
            'challenge' => $challenge,
        ]);
    }

    /**
     * Creates a new challenge.
     *
     * @param  \Illuminate\Http\Request        $request
     * @param  App\Services\ChallengeManager   $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postRegister(Request $request, ChallengeManager $service, $id)
    {
        if($log = $service->createChallengeLog(Challenge::active()->where('id', $id)->first(), Auth::user())) {
            flash('Registered successfully.')->success();
            return redirect()->to('challenges/view/'.$log->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('challenges');
    }

    /**
     * Views a challenge log by ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getLog(Request $request, $id)
    {
        $log = UserChallenge::find($id);
        if(!$log) abort(404);

        return view('challenges.log', [
            'log' => $log
        ]);
    }

    /**
     * Updates a log.
     *
     * @param  \Illuminate\Http\Request        $request
     * @param  App\Services\ChallengeManager   $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEditChallenge(Request $request, ChallengeManager $service, $id)
    {
        $request->validate(UserChallenge::$updateRules);
        $data = $request->only(['prompt_url', 'prompt_text']);

        if($service->editChallenge($data, UserChallenge::find($id), Auth::user())) {
            flash('Challenge updated successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Shows a list of the user's old challenges.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getList(Request $request)
    {
        $type = $request->get('type');
        $query = UserChallenge::where('user_id', Auth::user()->id)->where('status', $type ? ucfirst($type) : 'Active');

        $data = $request->only(['sort']);
        if(isset($data['sort']))
        {
            switch($data['sort']) {
                case 'newest':
                    $query->orderBy('created_at', 'DESC');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'ASC');
                    break;
            }
        }
        else $query->orderBy('created_at', 'DESC');

        return view('challenges.list', [
            'logs' => $query->paginate(20)->appends($request->query()),
        ]);
    }
}
