{!! Form::open(['url' => 'admin/character/image/'.$image->id.'/reupload', 'files' => true]) !!}
<div class="form-group">
        {!! Form::label('Character Image') !!} {!! add_help('This is the full masterlist image. Note that the image is not protected in any way, so take precautions to avoid art/design theft.') !!}
        <div>{!! Form::file('image', ['id' => 'mainImage']) !!}</div>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <div id="cropSelect">Select an image to use the cropper.</div>
            <img src="#" id="cropper" alt="" />
            {!! Form::hidden('x0', null, ['id' => 'cropX0']) !!}
            {!! Form::hidden('x1', null, ['id' => 'cropX1']) !!}
            {!! Form::hidden('y0', null, ['id' => 'cropY0']) !!}
            {!! Form::hidden('y1', null, ['id' => 'cropY1']) !!}
        </div>
    </div>
    <div class="form-group">
        {!! Form::checkbox('use_cropper', 1, 0, ['class' => 'form-check-input', 'data-toggle' => 'toggle', 'id' => 'useCropper']) !!}
        {!! Form::label('use_cropper', 'Upload Custom Thumbnail', ['class' => 'form-check-label ml-3']) !!}
    </div>
    <div class="card mb-3" id="thumbnailUpload">
        <div class="card-body">
            {!! Form::label('Thumbnail Image') !!} {!! add_help('This image is shown on the masterlist page.') !!}
            <div>{!! Form::file('thumbnail') !!}</div>
            <div class="text-muted">Recommended size: {{ Config::get('lorekeeper.settings.masterlist_thumbnails.width') }}px x {{ Config::get('lorekeeper.settings.masterlist_thumbnails.height') }}px</div>
        </div>
    </div>

    <div class="text-right">
        {!! Form::submit('Edit', ['class' => 'btn btn-primary']) !!}
    </div>
{!! Form::close() !!}

<script>
    $(document).ready(function() {
        //$('#useCropper').bootstrapToggle();

        // Cropper ////////////////////////////////////////////////////////////////////////////////////

        var $useCropper = $('#useCropper');
        var $thumbnailCrop = $('#thumbnailCrop');
        var $thumbnailUpload = $('#thumbnailUpload');

        var useCropper = $useCropper.is(':checked');

        updateCropper();

        $useCropper.on('change', function(e) {
            useCropper = $useCropper.is(':checked');

            updateCropper();
        });

        function updateCropper() {
            if(useCropper) {
                $thumbnailUpload.removeClass('hide');
            }
            else {
                $thumbnailUpload.addClass('hide');
            }
        }

        // Croppie ////////////////////////////////////////////////////////////////////////////////////

        var thumbnailWidth = {{ $frameHelper->contextWidth($image->species_id, $image->subtype_id); }};
        var thumbnailHeight = {{ $frameHelper->contextHeight($image->species_id, $image->subtype_id); }};
        var $cropper = $('#cropper');
        var c = null;
        var $x0 = $('#cropX0');
        var $y0 = $('#cropY0');
        var $x1 = $('#cropX1');
        var $y1 = $('#cropY1');
        var zoom = 0;

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $cropper.attr('src', e.target.result);
                    c = new Croppie($cropper[0], {
                        viewport: {
                            width: thumbnailWidth,
                            height: thumbnailHeight
                        },
                        boundary: { width: thumbnailWidth + 100, height: thumbnailHeight + 100 },
                        update: function() {
                            updateCropValues();
                        }
                    });
                    updateCropValues();
                    $('#cropSelect').addClass('hide');
                    $cropper.removeClass('hide');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#mainImage").change(function() {
            readURL(this);
        });

        function updateCropValues() {
            var values = c.get();
            $x0.val(values.points[0]);
            $y0.val(values.points[1]);
            $x1.val(values.points[2]);
            $y1.val(values.points[3]);
        }
    });

</script>
