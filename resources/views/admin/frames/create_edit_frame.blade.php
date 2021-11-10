@extends('admin.layout')

@section('admin-title') Frames @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Frames' => 'admin/data/frames', ($frame->id ? 'Edit' : 'Create').' Frame' => $frame->id ? 'admin/data/frames/edit/'.$frame->id : 'admin/data/frames/create']) !!}

<h1>{{ $frame->id ? 'Edit' : 'Create' }} Frame
    @if($frame->id)
        <a href="#" class="btn btn-outline-danger float-right delete-frame-button">Delete Frame</a>
    @endif
</h1>

{!! Form::open(['url' => $frame->id ? 'admin/data/frames/edit/'.$frame->id : 'admin/data/frames/create', 'files' => true]) !!}

<h3>Basic Information</h3>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('Name') !!}
            {!! Form::text('name', $frame->name, ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('Frame Category (Optional)') !!}
            {!! Form::select('frame_category_id', $categories, $frame->frame_category_id, ['class' => 'form-control']) !!}
        </div>
    </div>
</div>

<div class="form-group">
    {!! Form::label('Description (Optional)') !!}
    {!! Form::textarea('description', $frame->description, ['class' => 'form-control wysiwyg']) !!}
</div>

<div class="form-group">
    {!! Form::checkbox('is_default', 1, $frame->is_default, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
    {!! Form::label('is_default', 'Default Frame', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If enabled, this frame will be the default and automatically available to/used by default for all characters. Setting this when there is already a default frame will unset the existing default frame. This will be set by default if there is no current default frame.') !!}
</div>

<h3>Images</h3>

<p>
    The images here-- the frame itself as well as background-- are used for both display of the frame as well as for its application to characters. Note that all frames <strong>must</strong> be a consistent size that matches the site's configuration. If your frame has meaningfully different dimensions than your back in a dimension, it's recommended to save it with additional space around it so that the back and frame will line up if one is applied centered on top of the other. Any transparent space created as a consequence of this will be trimmed from the resulting image as part of processing, so this is purely to ensure the two images line up when applied.
</p>

<div class="row">
    <div class="col-md mb-4">
        <div class="form-group">
            {!! Form::label('Frame Image') !!}
            <div>{!! Form::file('frame_image') !!}</div>
            <div class="text-muted">Must be .png</div>
        </div>
        @if($frame->id)
            Current Image:
            <img src="{{ $frame->frameUrl }}" class="mw-100" />
        @endif
    </div>
    <div class="col-md mb-4">
        <div class="form-group">
            {!! Form::label('Back Image') !!}
            <div>{!! Form::file('back_image') !!}</div>
            <div class="text-muted">Must be .png, Configured size: {{ Config::get('lorekeeper.settings.frame_dimensions.width') }}px x {{ Config::get('lorekeeper.settings.frame_dimensions.height') }}px</div>
        </div>
        @if($frame->id)
            Current Image:
            <img src="{{ $frame->backUrl }}" class="mw-100" />
        @endif
    </div>
</div>

<div class="text-right">
    {!! Form::submit($frame->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}

@if($frame->id)
    <h3>Preview</h3>
    <div class="card mb-3">
        <div class="card-body">
            @include('world._frame_entry', ['imageUrl' => $frame->imageUrl, 'name' => $frame->displayName, 'description' => $frame->parsed_description, 'searchUrl' => $frame->searchUrl, 'isDefault' => $frame->is_default])
        </div>
    </div>
@endif

@endsection

@section('scripts')
@parent
<script>
$( document ).ready(function() {
    $('.selectize').selectize();

    $('.delete-frame-button').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('admin/data/frames/delete') }}/{{ $frame->id }}", 'Delete Frame');
    });
});

</script>
@endsection
