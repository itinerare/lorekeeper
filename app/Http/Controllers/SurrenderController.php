<?php

namespace App\Http\Controllers;

use Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Services\SurrenderManager;

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
    //
    /**
     * Shows surrender form
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSurrender()
    {
        $characters = Character::orderBy('id')->where('user_id', Auth::user()->id)->pluck('slug', 'id');
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
        $data = $request->only(['character_id', 'notes', 'worth']);

        if($service->createSurrender($data, Auth::user())) {
            flash('Surrender submitted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('surrender');
    }

}
