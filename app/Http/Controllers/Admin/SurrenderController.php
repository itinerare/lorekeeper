<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Config;
use Settings;
use Illuminate\Http\Request;

use App\Models\Rarity;
use App\Models\Adoption\Surrender;
use App\Models\Character\Character;
use App\Models\Currency\Currency;

use App\Services\SurrenderManager;

use App\Http\Controllers\Controller;

class SurrenderController extends Controller
{
    /**
     * Shows the surrender index page.
     *
     * @param  string  $status
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSurrenderIndex(Request $request, $status = null)
    {
        $surrender = Surrender::where('status', $status ? ucfirst($status) : 'Pending');
        $data = $request->only(['sort']);
        if(isset($data['sort'])) 
        {
            switch($data['sort']) {
                case 'newest':
                    $surrender->sortNewest();
                    break;
                case 'oldest':
                    $surrender->sortOldest();
                    break;
            }
        } 
        else $surrender->sortOldest();
        return view('admin.surrenders.index', [
            'surrender' => $surrender->paginate(30)->appends($request->query()),
        ]);
    }

    /**
     * Shows the surrender detail page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSurrender($id)
    {
        $surrender = Surrender::where('id', $id)->first();
        if(!$surrender) abort(404);
        
        if(Settings::get('calculate_by_traits')) {
        $totalcost = 0; // set this to be whatever your base price should be
        // getting all the traits for the character that the surrender form is for
        $features = $surrender->character->image->features()->get();
        // since a character can have multiple traits, we need to use a foreach to calculate each trait one by one 
        foreach ($features as $traits) {
            // find rarities attached to trait
            // You can also set this to something else , just make sure to change the variables
            $rarity = Rarity::where('id', $traits->rarity_id)->first();

            switch ($rarity->name) {
                // e.g if the rarity name returns rare, the cost is 100
                // the following are example / placeholder worth
                case 'common':
                    $totalcost += 10;
                break;
                case 'uncommon':
                    $totalcost += 50;
                break;
                case 'rare':
                    $totalcost += 100;
                break;
                }
            }
        }
        else {
            $totalcost = null;
        }
        return view('admin.surrenders.surrender', [
            'surrender' => $surrender,
            'estimate' => $totalcost,
        ] + ($surrender->status == 'Pending' ? [
            'worth' => Currency::find($surrender->currency_id),
            'currencies' => Currency::where('is_user_owned', 1)->orderBy('name')->pluck('name', 'id'),
            'count' => Surrender::where('status', 'Approved')->where('user_id', $surrender->user_id)->count()
        ] : []));
    }

    /**
     * 
     * Approves / rejects surrender and distributes rewards etc etc
     */
    public function postSurrender(Request $request, SurrenderManager $service, $id, $action)
    {
        $data = $request->only(['grant', 'staff_comments', 'currency_id']);
        if($action == 'reject' && $service->rejectSurrender($data + ['id' => $id], Auth::user())) {
            flash('Surrender rejected successfully.')->success();
            }
            elseif($action == 'approve' && $service->approveSurrender($data + ['id' => $id], Auth::user())) {
                flash('Surrender approved successfully. Make sure to make it visible when you\'re ready!')->success();
                return redirect()->to('admin/data/stock');
            }
            else {
                foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
            }
            return redirect()->back();
    }
}