<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Config;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Models\Challenge\Challenge;
use App\Models\Challenge\UserChallenge;

use App\Services\ChallengeManager;

use App\Http\Controllers\Controller;

class ChallengeController extends Controller
{
    /**
     * Shows the user challenge index page.
     *
     * @param  string  $status
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex(Request $request, $status = null)
    {
        if($status == 'old') $challenges = UserChallenge::old();
        else $challenges = UserChallenge::where('status', $status ? ucfirst($status) : 'Active');

        return view('admin.challenges.challenges', [
            'challenges' => $challenges->orderBy('id', 'DESC')->paginate(30),
        ]);
    }

    /**
     * Edits a challenge.
     *
     * @param  \Illuminate\Http\Request        $request
     * @param  App\Services\ChallengeManager   $service
     * @param  int                             $id
     * @param  string                          $action
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postChallenge(Request $request, ChallengeManager $service, $id, $action)
    {
        if($action == 'accept' && $service->acceptChallenge($request->only(['staff_comments']), UserChallenge::find($id), Auth::user())) {
            flash('Log accepted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
}
