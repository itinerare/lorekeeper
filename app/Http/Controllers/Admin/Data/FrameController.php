<?php

namespace App\Http\Controllers\Admin\Data;

use Illuminate\Http\Request;

use Auth;
use Config;

use App\Models\Frame\FrameCategory;
use App\Models\Frame\Frame;
use App\Models\User\User;

use App\Models\Species\Species;
use App\Models\Species\Subtype;

use App\Services\FrameService;

use App\Http\Controllers\Controller;

class FrameController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Admin / Frame Controller
    |--------------------------------------------------------------------------
    |
    | Handles creation/editing of frames.
    |
    */

    /**********************************************************************************************

        FRAME CATEGORIES

    **********************************************************************************************/

    /**
     * Shows the frame category index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        return view('admin.frames.frame_categories', [
            'categories' => FrameCategory::orderBy('sort', 'DESC')->get()
        ]);
    }

    /**
     * Shows the create frame category page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateFrameCategory()
    {
        return view('admin.frames.create_edit_frame_category', [
            'category' => new FrameCategory
        ]);
    }

    /**
     * Shows the edit frame category page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditFrameCategory($id)
    {
        $category = FrameCategory::find($id);
        if(!$category) abort(404);
        return view('admin.frames.create_edit_frame_category', [
            'category' => $category
        ]);
    }

    /**
     * Creates or edits an frame category.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\FrameService  $service
     * @param  int|null                  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditFrameCategory(Request $request, FrameService $service, $id = null)
    {
        $id ? $request->validate(FrameCategory::$updateRules) : $request->validate(FrameCategory::$createRules);
        $data = $request->only([
            'name', 'description', 'image', 'remove_image', 'is_character_owned', 'character_limit', 'can_name'
        ]);
        if($id && $service->updateFrameCategory(FrameCategory::find($id), $data, Auth::user())) {
            flash('Category updated successfully.')->success();
        }
        else if (!$id && $category = $service->createFrameCategory($data, Auth::user())) {
            flash('Category created successfully.')->success();
            return redirect()->to('admin/data/frame-categories/edit/'.$category->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Gets the frame category deletion modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteFrameCategory($id)
    {
        $category = FrameCategory::find($id);
        return view('admin.frames._delete_frame_category', [
            'category' => $category,
        ]);
    }

    /**
     * Deletes an frame category.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\FrameService  $service
     * @param  int                       $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteFrameCategory(Request $request, FrameService $service, $id)
    {
        if($id && $service->deleteFrameCategory(FrameCategory::find($id))) {
            flash('Category deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/data/frame-categories');
    }

    /**
     * Sorts frame categories.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\FrameService  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postSortFrameCategory(Request $request, FrameService $service)
    {
        if($service->sortFrameCategory($request->get('sort'))) {
            flash('Category order updated successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**********************************************************************************************

        FRAMES

    **********************************************************************************************/

    /**
     * Shows the frame index.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getFrameIndex(Request $request)
    {
        $query = Frame::query();
        $data = $request->only(['name', 'frame_category_id']);
        if(isset($data['frame_category_id']) && $data['frame_category_id'] != 'none')
            $query->where('frame_category_id', $data['frame_category_id']);
        if(isset($data['name']))
            $query->where('name', 'LIKE', '%'.$data['name'].'%');

        // Gather configured per-species and -subtype sizes
        $sizes = collect(Config::get('lorekeeper.settings.frame_dimensions'))->filter(function($setting, $key) {
            return is_numeric($key);
        });

        foreach($sizes as $key=>$size) {
            $sizeArray['species'][$key] = [
                'species' => Species::where('id', $key)->first() ? Species::where('id', $key)->first() : 'Invalid Species',
                'default_frame' => (new Frame)->defaultFrame($key, null) ? 1 : 0,
                'width' => $size['width'],
                'height' => $size['height']
            ];

            foreach($size as $subtypeKey=>$subtypeSize) {
                if(is_numeric($subtypeKey)) {
                    $sizeArray['subtype'][$key][$subtypeKey] = [
                        'subtype' => Subtype::where('species_id', $key)->where('id', $subtypeKey)->first() ? Subtype::where('species_id', $key)->where('id', $subtypeKey)->first() : 'Invalid Subtype',
                        'default_frame' => (new Frame)->defaultFrame($key, $subtypeKey) ? 1 : 0,
                        'width' => $subtypeSize['width'],
                        'height' => $subtypeSize['height']
                    ];
                }
            }
        }

        return view('admin.frames.frames', [
            'frames' => $query->paginate(20)->appends($request->query()),
            'categories' => ['none' => 'Any Category'] + FrameCategory::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'defaultFrame' => Frame::whereNull('species_id')->where('is_default', 1)->first() ? 1 : 0,
            'sizes' => $sizeArray,
        ]);
    }

    /**
     * Shows the create frame page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateFrame()
    {
        return view('admin.frames.create_edit_frame', [
            'frame' => new Frame,
            'categories' => ['none' => 'No category'] + FrameCategory::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'specieses' => [null => 'Any Species'] + Species::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'subtypes' => [null => 'Any Subtype'] + Subtype::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Shows the edit frame page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditFrame($id)
    {
        $frame = Frame::find($id);
        if(!$frame) abort(404);

        return view('admin.frames.create_edit_frame', [
            'frame' => $frame,
            'categories' => ['none' => 'No category'] + FrameCategory::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'specieses' => [null => 'Any Species'] + Species::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'subtypes' => [null => 'Any Subtype'] + Subtype::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Creates or edits an frame.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\FrameService  $service
     * @param  int|null                  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditFrame(Request $request, FrameService $service, $id = null)
    {
        $id ? $request->validate(Frame::$updateRules) : $request->validate(Frame::$createRules);
        $data = $request->only([
            'name', 'description', 'frame_category_id', 'is_default', 'frame_image', 'back_image', 'item_id', 'species_id', 'subtype_id'
        ]);
        if($id && $service->updateFrame(Frame::find($id), $data, Auth::user())) {
            flash('Frame updated successfully.')->success();
        }
        else if (!$id && $frame = $service->createFrame($data, Auth::user())) {
            flash('Frame created successfully.')->success();
            return redirect()->to('admin/data/frames/edit/'.$frame->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Gets the frame deletion modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteFrame($id)
    {
        $frame = Frame::find($id);
        return view('admin.frames._delete_frame', [
            'frame' => $frame,
        ]);
    }

    /**
     * Creates or edits an frame.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\FrameService  $service
     * @param  int                       $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteFrame(Request $request, FrameService $service, $id)
    {
        if($id && $service->deleteFrame(Frame::find($id))) {
            flash('Frame deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/data/frames');
    }
}
