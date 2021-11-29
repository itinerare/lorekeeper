@extends('admin.layout')

@section('admin-title') Status Effects @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Status Effects' => 'admin/data/status-effects', ($status->id ? 'Edit' : 'Create').' Status Effect' => $status->id ? 'admin/data/status-effects/edit/'.$status->id : 'admin/data/status-effects/create']) !!}

<h1>{{ $status->id ? 'Edit' : 'Create' }} Status Effect
    @if($status->id)
        <a href="#" class="btn btn-danger float-right delete-status-button">Delete Status Effect</a>
    @endif
</h1>

{!! Form::open(['url' => $status->id ? 'admin/data/status-effects/edit/'.$status->id : 'admin/data/status-effects/create', 'files' => 'true']) !!}

<h3>Basic Information</h3>
<div class="form-group">
    {!! Form::label('Status Effect Name') !!}
    {!! Form::text('name', $status->name, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('World Page Image (Optional)') !!} {!! add_help('This image is used only on the world information pages.') !!}
    <div>{!! Form::file('image') !!}</div>
    <div class="text-muted">Recommended size: 100px x 100px</div>
    @if($status->has_image)
        <div class="form-check">
            {!! Form::checkbox('remove_image', 1, false, ['class' => 'form-check-input']) !!}
            {!! Form::label('remove_image', 'Remove current image', ['class' => 'form-check-label']) !!}
        </div>
    @endif
</div>

<div class="form-group">
    {!! Form::label('Description (Optional)') !!}
    {!! Form::textarea('description', $status->description, ['class' => 'form-control wysiwyg']) !!}
</div>

<h3>Severity</h3>
<p>Here you can specify different breakpoints in severity, at or above which the name of the status effect as displayed will be replaced with the name specified here.</p>

<div class="form-group">
    <div id="severityList">
        @if($status->id)
            @foreach($status->data as $severity)
                <div class="my-2">
                    <div class="input-group">
                        {!! Form::text('severity_name[]', $severity['name'], ['class' => 'form-control', 'placeholder' => 'Name', 'aria-label' => 'Severity name', 'aria-describedby' => 'severity-name-group']) !!}
                        {!! Form::number('severity_breakpoint[]', $severity['breakpoint'], ['class' => 'form-control', 'placeholder' => 'Breakpoint', 'aria-label' => 'Severity breakpoint', 'aria-describedby' => 'severity-name-group']) !!}
                        <div class="input-group-append">
                            <button class="btn btn-outline-danger remove-severity" type="button" id="severity-name-group">Remove Severity</button>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
    <div class="text-right"><a href="#" class="btn btn-primary" id="add-severity">Add Severity</a></div>
</div>

<div class="text-right">
    {!! Form::submit($status->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}

<div class="severity-row hide my-2">
    <div class="input-group">
        {!! Form::text('severity_name[]', null, ['class' => 'form-control', 'placeholder' => 'Name', 'aria-label' => 'Severity name', 'aria-describedby' => 'severity-name-group']) !!}
        {!! Form::number('severity_breakpoint[]', null, ['class' => 'form-control', 'placeholder' => 'Breakpoint', 'aria-label' => 'Severity breakpoint', 'aria-describedby' => 'severity-name-group']) !!}
        <div class="input-group-append">
            <button class="btn btn-outline-danger remove-severity" type="button" id="severity-name-group">Remove Severity</button>
        </div>
    </div>
</div>

@if($status->id)
    <h3>Preview</h3>
    <div class="card mb-3">
        <div class="card-body">
            @include('world._entry', ['imageUrl' => $status->imageUrl, 'name' => $status->displayName, 'description' => $status->parsed_description])
        </div>
    </div>
@endif

@endsection

@section('scripts')
@parent
<script>
$( document ).ready(function() {
    $('#add-severity').on('click', function(e) {
        e.preventDefault();
        addSeverityRow();
    });
    $('.remove-severity').on('click', function(e) {
        e.preventDefault();
        removeSeverityRow($(this));
    })
    function addSeverityRow() {
        var $clone = $('.severity-row').clone();
        $('#severityList').append($clone);
        $clone.removeClass('hide severity-row');
        $clone.addClass('d-flex');
        $clone.find('.remove-severity').on('click', function(e) {
            e.preventDefault();
            removeSeverityRow($(this));
        })
        $clone.find('.severity-select').selectize();
    }
    function removeSeverityRow($trigger) {
        $trigger.parent().parent().remove();
    }

    $('.delete-status-button').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('admin/data/status-effects/delete') }}/{{ $status->id }}", 'Delete Status Effect');
    });
});

</script>
@endsection
