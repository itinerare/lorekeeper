<?php

namespace App\Http\Controllers\Admin\Data;

use Illuminate\Http\Request;

use Auth;

use App\Models\Status\StatusEffect;
use App\Services\StatusEffectService;

use App\Http\Controllers\Controller;

class StatusController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Admin / Status Effect Controller
    |--------------------------------------------------------------------------
    |
    | Handles creation/editing of status effects.
    |
    */

    /**
     * Shows the status effect index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        return view('admin.statuses.statuses', [
            'statuses' => StatusEffect::paginate(30)
        ]);
    }

    /**
     * Shows the create status effect page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateStatusEffect()
    {
        return view('admin.statuses.create_edit_status', [
            'status' => new StatusEffect
        ]);
    }

    /**
     * Shows the edit status effect page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditStatusEffect($id)
    {
        $statusEffect = StatusEffect::find($id);
        if(!$statusEffect) abort(404);
        return view('admin.statuses.create_edit_status', [
            'status' => $statusEffect
        ]);
    }

    /**
     * Creates or edits a status effect.
     *
     * @param  \Illuminate\Http\Request               $request
     * @param  App\Services\CharacterCategoryService  $service
     * @param  int|null                               $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditStatusEffect(Request $request, StatusEffectService $service, $id = null)
    {
        $id ? $request->validate(StatusEffect::$updateRules) : $request->validate(StatusEffect::$createRules);
        $data = $request->only([
            'name', 'description', 'image',
            'severity_name', 'severity_breakpoint',
        ]);
        if($id && $service->updateStatusEffect(StatusEffect::find($id), $data, Auth::user())) {
            flash('Status effect updated successfully.')->success();
        }
        else if (!$id && $statusEffect = $service->createStatusEffect($data, Auth::user())) {
            flash('Status effect created successfully.')->success();
            return redirect()->to('admin/data/status-effects/edit/'.$statusEffect->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Gets the status effect deletion modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteStatusEffect($id)
    {
        $statusEffect = StatusEffect::find($id);
        return view('admin.statuses._delete_status', [
            'status' => $statusEffect,
        ]);
    }

    /**
     * Deletes a status effect.
     *
     * @param  \Illuminate\Http\Request               $request
     * @param  App\Services\StatusEffectService       $service
     * @param  int                                    $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteStatusEffect(Request $request, StatusEffectService $service, $id)
    {
        if($id && $service->deleteStatusEffect(StatusEffect::find($id))) {
            flash('StatusEffect deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/data/status-effects');
    }
}
