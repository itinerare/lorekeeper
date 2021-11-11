<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;
use Image;

use App\Models\Frame\FrameCategory;
use App\Models\Frame\Frame;
use App\Models\Character\CharacterFrame;

class FrameService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Frame Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of frame categories and frames.
    |
    */

    /**********************************************************************************************

        FRAME CATEGORIES

    **********************************************************************************************/

    /**
     * Create a category.
     *
     * @param  array                 $data
     * @param  \App\Models\User\User $user
     * @return \App\Models\Frame\FrameCategory|bool
     */
    public function createFrameCategory($data, $user)
    {
        DB::beginTransaction();

        try {

            $data = $this->populateCategoryData($data);

            $image = null;
            if(isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }
            else $data['has_image'] = 0;

            $category = FrameCategory::create($data);

            if ($image) $this->handleImage($image, $category->categoryImagePath, $category->categoryImageFileName);

            return $this->commitReturn($category);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Update a category.
     *
     * @param  \App\Models\Frame\FrameCategory  $category
     * @param  array                          $data
     * @param  \App\Models\User\User          $user
     * @return \App\Models\Frame\FrameCategory|bool
     */
    public function updateFrameCategory($category, $data, $user)
    {
        DB::beginTransaction();

        try {
            // More specific validation
            if(FrameCategory::where('name', $data['name'])->where('id', '!=', $category->id)->exists()) throw new \Exception("The name has already been taken.");

            $data = $this->populateCategoryData($data, $category);

            $image = null;
            if(isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }

            $category->update($data);

            if ($category) $this->handleImage($image, $category->categoryImagePath, $category->categoryImageFileName);

            return $this->commitReturn($category);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Handle category data.
     *
     * @param  array                               $data
     * @param  \App\Models\Frame\FrameCategory|null  $category
     * @return array
     */
    private function populateCategoryData($data, $category = null)
    {
        if(isset($data['description']) && $data['description'])
            $data['parsed_description'] = parse($data['description']);
        else $data['parsed_description'] = null;

        if(isset($data['remove_image']))
        {
            if($category && $category->has_image && $data['remove_image'])
            {
                $data['has_image'] = 0;
                $this->deleteImage($category->categoryImagePath, $category->categoryImageFileName);
            }
            unset($data['remove_image']);
        }

        return $data;
    }

    /**
     * Delete a category.
     *
     * @param  \App\Models\Frame\FrameCategory  $category
     * @return bool
     */
    public function deleteFrameCategory($category)
    {
        DB::beginTransaction();

        try {
            // Check first if the category is currently in use
            if(Frame::where('frame_category_id', $category->id)->exists()) throw new \Exception("An frame with this category exists. Please change its category first.");

            if($category->has_image) $this->deleteImage($category->categoryImagePath, $category->categoryImageFileName);
            $category->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Sorts category order.
     *
     * @param  array  $data
     * @return bool
     */
    public function sortFrameCategory($data)
    {
        DB::beginTransaction();

        try {
            // explode the sort array and reverse it since the order is inverted
            $sort = array_reverse(explode(',', $data));

            foreach($sort as $key => $s) {
                FrameCategory::where('id', $s)->update(['sort' => $key]);
            }

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**********************************************************************************************

        FRAMES

    **********************************************************************************************/

    /**
     * Creates a new frame.
     *
     * @param  array                  $data
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\Frame\Frame
     */
    public function createFrame($data, $user)
    {
        DB::beginTransaction();

        try {
            if(isset($data['frame_category_id']) && $data['frame_category_id'] == 'none') $data['frame_category_id'] = null;

            if((isset($data['frame_category_id']) && $data['frame_category_id']) && !FrameCategory::where('id', $data['frame_category_id'])->exists()) throw new \Exception("The selected frame category is invalid.");

            $data = $this->populateData($data);

            $data['item_id'] = 1;

            $frame = Frame::create($data);

            if(!$this->processFrameImage($data, $frame)) throw new \Exception('Failed to process frame images.');

            return $this->commitReturn($frame);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Updates a frame.
     *
     * @param  \App\Models\Frame\Frame  $frame
     * @param  array                  $data
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\Frame\Frame
     */
    public function updateFrame($frame, $data, $user)
    {
        DB::beginTransaction();

        try {
            if(isset($data['frame_category_id']) && $data['frame_category_id'] == 'none') $data['frame_category_id'] = null;

            // More specific validation
            if(Frame::where('name', $data['name'])->where('id', '!=', $frame->id)->exists()) throw new \Exception("The name has already been taken.");
            if((isset($data['frame_category_id']) && $data['frame_category_id']) && !FrameCategory::where('id', $data['frame_category_id'])->exists()) throw new \Exception("The selected frame category is invalid.");

            $data = $this->populateData($data, $frame);

            // If either image is being reuploaded, process
            if(isset($data['frame_image']) || isset($data['back_image'])) {
                if(!$this->processFrameImage($data, $frame)) throw new \Exception('Failed to process frame images.');
            }

            $frame->update($data);

            return $this->commitReturn($frame);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Processes user input for creating/updating a frame.
     *
     * @param  array                   $data
     * @param  \App\Models\Frame\Frame $frame
     * @return array
     */
    private function populateData($data, $frame = null)
    {
        if(isset($data['description']) && $data['description'])
            $data['parsed_description'] = parse($data['description']);
        else $data['parsed_description'] = null;

        // Check toggle
        if(!isset($data['is_default']))
            $data['is_default'] = 0;
        // But set it if there is no current default frame
        if(!Frame::where('is_default', 1)->count())
            $data['is_default'] = 1;
        // If there's a pre-existing default frame, unset it
        elseif(($frame && Frame::where('id', '!=', $frame->id)->where('is_default', 1)->count()) || (!$frame && Frame::where('is_default', 1)->count())) {
            if($frame) Frame::where('id', '!=', $frame->id)->where('is_default', 1)->update(['is_default' => 0]);
            else Frame::where('is_default', 1)->update(['is_default' => 0]);
        }

        // If the frame is new, set a hash
        if(!$frame)
            $data['hash'] = randomString(15);

        return $data;
    }

    /**
     * Processes images for creating/updating a frame.
     *
     * @param  array                   $data
     * @param  \App\Models\Frame\Frame $frame
     * @return array
     */
    private function processFrameImage($data, $frame)
    {
        try {
            // First, save both new frame and/or back images verbatim
            if(isset($data['frame_image']))
                $this->handleImage($data['frame_image'], $frame->imagePath, $frame->frameFileName);
            if(isset($data['back_image']))
                $this->handleImage($data['back_image'], $frame->imagePath, $frame->backFileName);

            // Next, create a composite, the fully assembled frame, for display purposes
            // Start by creating image objects of both the back and frame
            $backImage = Image::make($frame->imagePath.'/'.$frame->backFileName);
            $frontImage = Image::make($frame->imagePath.'/'.$frame->frameFileName);

            // The frame is most likely to have larger dimensions than the back,
            // so resize the back image's canvas based on the frame's dimensions
            $composite = $backImage->resizeCanvas($frontImage->width(), $frontImage->height());

            // Then insert the frame and trim transparent space
            $composite->insert($frontImage, 'center')->trim('transparent');

            // Finally, save the composite frame image
            $composite->save($frame->imagePath.'/'.$frame->imageFileName);

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }


    /**
     * Deletes a frame.
     *
     * @param  \App\Models\Frame\Frame  $frame
     * @return bool
     */
    public function deleteFrame($frame)
    {
        DB::beginTransaction();

        try {
            // Check first if the frame is currently owned or if some other site feature uses it
            if($frame->is_default) throw new \Exception('This frame is currently the default frame. Please set a different default frame before deleting it.');
            if(CharacterFrame::where('frame_id', $frame->id)->exists()) throw new \Exception("At least one character currently owns this frame. Please remove the frame(s) before deleting it.");

            DB::table('character_frames')->where('frame_id', $frame->id)->delete();

            // Delete images
            $this->deleteImage($frame->imagePath, $frame->imageFileName);
            $this->deleteImage($frame->imagePath, $frame->frameFileName);
            $this->deleteImage($frame->imagePath, $frame->backFileName);

            // Delete the frame itself
            $frame->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
}
