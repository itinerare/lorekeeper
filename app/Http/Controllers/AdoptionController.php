<?php

namespace App\Http\Controllers;

use Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Services\AdoptionManager;

use App\Models\Adoption\Adoption;
use App\Models\Adoption\AdoptionStock;
use App\Models\Adoption\AdoptionLog;
use App\Models\Adoption\AdoptionCurrency;
use App\Models\Character\Character;
use App\Models\Character\CharacterCategory;
use App\Models\Currency\Currency;

class AdoptionController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Adoption Controller
    |--------------------------------------------------------------------------
    |
    | Handles viewing the adoption index, adoptions and purchasing from adoptions.
    |
    */
    
    /**
     * Shows a adoption.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getAdoption()
    {
        $categories = CharacterCategory::orderBy('sort', 'DESC')->get();
        $adoption = Adoption::where('id', 1)->where('is_active', 1)->first();
        if(!$adoption) abort(404);
        $characters = count($categories) ? $adoption->displayStock()->orderByRaw('FIELD(character_category_id,'.implode(',', $categories->pluck('id')->toArray()).')')->orderBy('name')->get()->groupBy('character_category_id') : $adoption->displayStock()->orderBy('name')->get()->groupBy('character_category_id');
        return view('adoptions.adoption', [
            'adoption' => $adoption,
            'characters' => $characters,
            'categories' => $categories->keyBy('id'),
            'adoptions' => Adoption::where('is_active', 1)->get(),
            'currencies' => Currency::whereIn('id', AdoptionCurrency::pluck('currency_id')->toArray())->get()->keyBy('id')
        ]);
    }

    /**
     * Gets the adoption stock modal.
     *
     * @param  App\Services\AdoptionManager  $service
     * @param  int                       $id
     * @param  int                       $stockId
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getAdoptionStock(AdoptionManager $service, $id, $stockId)
    {
        $adoption = Adoption::where('id', $id)->where('is_active', 1)->first();
        if(!$adoption) abort(404);
        return view('adoptions._stock_modal', [
            'adoption' => $adoption,
            'stock' => $stock = AdoptionStock::with('character')->where('id', $stockId)->where('adoption_id', $id)->first(),
            'purchaseLimitReached' => $service->checkPurchaseLimitReached($stock, Auth::user())
        ]);
    }

    /**
     * Buys an character from a adoption.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\AdoptionManager  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postBuy(Request $request, AdoptionManager $service)
    {
        $request->validate(AdoptionLog::$createRules);
        if($service->buyStock($request->only(['stock_id', 'adoption_id', 'slug', 'bank']), Auth::user())) {
            flash('Successfully purchased character.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Shows the user's purchase history.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getPurchaseHistory()
    {
        return view('adoptions.purchase_history', [
            'logs' => Auth::user()->getAdoptionLogs(0),
            'adoptions' => Adoption::where('is_active', 1)->orderBy('sort', 'DESC')->get(),
        ]);
    }
}


