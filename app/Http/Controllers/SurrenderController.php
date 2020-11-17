<?php

namespace App\Http\Controllers;

use Auth;
use Settings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Services\SurrenderManager;

use App\Models\Rarity;
use App\Models\Adoption\Adoption;
use App\Models\Adoption\Surrender;
use App\Models\Adoption\AdoptionStock;
use App\Models\Adoption\AdoptionLog;
use App\Models\Adoption\AdoptionCurrency;
use App\Models\Character\Character;
use App\Models\Character\CharacterCategory;
use App\Models\Currency\Currency;

class SurrenderController extends Controller
{
    /**
     * Shows the user's surrender log.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex(Request $request)
    {
        $surrenders = Surrender::where('user_id', Auth::user()->id);
        $type = $request->get('type');
        if(!$type) $type = 'Pending';
        
        $surrenders = $surrenders->where('status', ucfirst($type));

        return view('home.surrenders', [
            'surrender' => $surrenders->orderBy('id', 'DESC')->paginate(20)->appends($request->query()),
        ]);
    }

    //
    /**
     * Shows surrender form
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSurrender()
    {
        $characters = Character::orderBy('id')->get()->where('user_id', Auth::user()->id)->pluck('fullname', 'id');
        $adoption = Adoption::where('id', 1)->where('is_active', 1)->first();
        if(!$adoption) abort(404);
        return view('adoptions.surrender_form', [
            'adoption' => $adoption,
            'characters' => $characters,
            'adoptions' => Adoption::where('is_active', 1)->get(),
            'currencies' => Currency::orderBy('name')->pluck('name', 'id'),
        ]);
    }
    /**
     * posts surrender form
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function postSurrender(Request $request, SurrenderManager $service) {

        $request->validate(Surrender::$createRules);
        $data = $request->only(['character_id', 'notes', 'worth', 'currency_id']);

        if($service->createSurrender($data, Auth::user())) {
            flash('Surrender submitted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('surrenders');
    }

    /**
     * Shows the surrender page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getPublicSurrender($id)
    {
        $surrender = Surrender::viewable(Auth::user())->where('id', $id)->first();
        if(!$surrender) abort(404);
        $features = $surrender->character->image->features()->get();
        
        $totalcost = 0; 
        foreach ($features as $traits) {
            $rarity = Rarity::where('id', $traits->rarity_id)->first();

            switch ($rarity->name) {
                
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
        return view('home.surrender', [
            'estimate' => $totalcost,
            'surrender' => $surrender,
            'user' => $surrender->user,
            'worth' => Currency::find($surrender->currency_id),
        ]);
    }

}
