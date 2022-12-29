@extends('admin.layout')

@section('admin-title') Traits @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Traits' => 'admin/data/traits', ($feature->id ? 'Edit' : 'Create').' Trait' => $feature->id ? 'admin/data/traits/edit/'.$feature->id : 'admin/data/traits/create']) !!}

<h1>{{ $feature->id ? 'Edit' : 'Create' }} Trait
    @if($feature->id)
        <a href="#" class="btn btn-danger float-right delete-feature-button">Delete Trait</a>
    @endif
</h1>

{!! Form::open(['url' => $feature->id ? 'admin/data/traits/edit/'.$feature->id : 'admin/data/traits/create', 'files' => true]) !!}

<h3>Basic Information</h3>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('Name') !!}
            {!! Form::text('name', $feature->name, ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('Rarity') !!}
            {!! Form::select('rarity_id', $rarities, $feature->rarity_id, ['class' => 'form-control']) !!}
        </div>
    </div>
</div>

@if($feature->altTypes->count())
    <div class="form-group">
        {!! Form::label('Display Mode') !!} {!! add_help("This controls how this trait's name will be displayed around the site. 'Name' refers to this type's name. Other values refer to this trait's settings.") !!}
        {!! Form::select('display_mode', [
            0 => 'Name', 1 => 'Name (Species)',
            2 => 'Name (Subtype)'
        ], $feature->display_mode, ['class' => 'form-control']) !!}
    </div>
@endif

<div class="form-group">
    {!! Form::label('World Page Image (Optional)') !!} {!! add_help('This image is used only on the world information pages.') !!}
    <div>{!! Form::file('image') !!}</div>
    <div class="text-muted">Recommended size: 200px x 200px</div>
    @if($feature->has_image)
        <div class="form-check">
            {!! Form::checkbox('remove_image', 1, false, ['class' => 'form-check-input']) !!}
            {!! Form::label('remove_image', 'Remove current image', ['class' => 'form-check-label']) !!}
        </div>
    @endif
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('Trait Category (Optional)') !!}
            {!! Form::select('feature_category_id', $categories, $feature->feature_category_id, ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('Species Restriction (Optional)') !!}
            {!! Form::select('species_id', $specieses, $feature->species_id, ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('Subtype (Optional)') !!} {!! add_help('This is cosmetic and does not limit choice of traits in selections.') !!}
            {!! Form::select('subtype_id', $subtypes, $feature->subtype_id, ['class' => 'form-control']) !!}
        </div>
    </div>
</div>
<div class="form-group">
    {!! Form::label('Description (Optional)') !!}
    {!! Form::textarea('description', $feature->description, ['class' => 'form-control wysiwyg']) !!}
</div>

@if($feature->id)
    <h3>Alternate Types</h3>

    <p>
        Here you can add alternate types for this trait. These are fully-featured traits, and can share or have different characteristics from their parent trait with the exception of category. They may also have the same name as their parent trait so long as their rarity or species are different. Certain information, such as the description, can only be adjusted once the type is created.
    </p>

    <div id="typeList">
        @foreach($feature->altTypes as $altType)
            <div class="card mb-3">
                <div class="card-body">
                    <h4 class="mb-4">
                        <div class="float-right">
                            <a href="#" class="remove-type btn btn-danger mb-2">X</a>
                        </div>
                        Basic Information
                    </h4>
                    {!! Form::hidden('alt[id]['.$altType->id.']', $altType->id) !!}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('Name') !!}
                                {!! Form::text('alt[name]['.$altType->id.']', $altType->name, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('Rarity') !!}
                                {!! Form::select('alt[rarity_id]['.$altType->id.']', $rarities, $altType->rarity_id, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                                {!! Form::label('Display Mode') !!} {!! add_help("This controls how this alternate type's name will be displayed around the site. 'Name' refers to this type's name, whereas 'Parent Name' refers to the parent trait's name. Other values refer to this type's settings.") !!}
                                {!! Form::select('alt[display_mode]['.$altType->id.']', [
                                        0 => 'Name', 1 => 'Name (Species)',
                                        2 => 'Name (Subtype)', 3 => 'Parent Name (Name)',
                                        4 => 'Name Parent Name',
                                    ], $altType->display_mode, ['class' => 'form-control']) !!}
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('Trait Category (Optional)') !!}
                                {!! Form::select('alt[feature_category_id]['.$altType->id.']', $categories, $altType->feature_category_id, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('Species Restriction (Optional)') !!}
                                {!! Form::select('alt[species_id]['.$altType->id.']', $specieses, $altType->species_id, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('Subtype (Optional)') !!} {!! add_help('This is cosmetic and does not limit choice of traits in selections.') !!}
                                {!! Form::select('alt[subtype_id]['.$altType->id.']', $subtypes, $altType->subtype_id, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('World Page Image (Optional)') !!} {!! add_help('This image is used only on the world information pages.') !!}
                        <div>{!! Form::file('alt[image]['.$altType->id.']') !!}</div>
                        <div class="text-muted">Recommended size: 200px x 200px</div>
                        @if($altType->has_image)
                            <div class="form-check">
                                {!! Form::checkbox('alt[remove_image]['.$altType->id.']', 1, false, ['class' => 'form-check-input']) !!}
                                {!! Form::label('alt[remove_image]['.$altType->id.']', 'Remove current image', ['class' => 'form-check-label']) !!}
                            </div>
                        @endif
                    </div>
                    <div class="form-group">
                        {!! Form::label('Description (Optional)') !!}
                        {!! Form::textarea('alt[description]['.$altType->id.']', $altType->description, ['class' => 'form-control wysiwyg']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::checkbox('alt[display_separate]['.$altType->id.']', 1, $altType->display_separate, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                        {!! Form::label('alt[display_separate]', 'Display Separately', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If enabled, this trait will be displayed separately from its parent in general trait listings, including species\' visual trait indexes if enabled.') !!}
                    </div>
                    @if(isset($altType->display_separate) && $altType->display_separate)
                        <h4>Preview</h4>
                        <hr/>
                        @include('world._feature_entry', ['feature' => $altType])
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="text-right mb-4">
        <a href="#" class="btn btn-primary" id="add-type">Add Alternate Type</a>
    </div>
@endif

<div class="text-right">
    {!! Form::submit($feature->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}

@if($feature->id)
    <div class="type-row hide mb-3">
        <div class="card mb-2">
            <div class="card-body">
                <h4 class="mb-4">
                    <div class="float-right">
                        <a href="#" class="remove-type btn btn-danger mb-2">X</a>
                    </div>
                    Basic Information
                </h4>
                {!! Form::hidden('alt[id][]', null) !!}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('Name') !!}
                            {!! Form::text('alt[name][]', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('Rarity') !!}
                            {!! Form::select('alt[rarity_id][]', $rarities, null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                            {!! Form::label('Display Mode') !!} {!! add_help("This controls how this alternate type's name will be displayed around the site. 'Name' refers to this type's name, whereas 'Parent Name' refers to the parent trait's name. Other values refer to this type's settings.") !!}
                            {!! Form::select('alt[display_mode][]', [
                                0 => 'Name', 1 => 'Name (Species)',
                                2 => 'Name (Subtype)', 3 => 'Parent Name (Name)',
                                4 => 'Name Parent Name',
                            ], 0, ['class' => 'form-control']) !!}
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('Trait Category (Optional)') !!}
                            {!! Form::select('alt[feature_category_id][]', $categories, null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('Species Restriction (Optional)') !!}
                            {!! Form::select('alt[species_id][]', $specieses, null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('Subtype (Optional)') !!} {!! add_help('This is cosmetic and does not limit choice of traits in selections.') !!}
                            {!! Form::select('alt[subtype_id][]', $subtypes, null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('World Page Image (Optional)') !!} {!! add_help('This image is used only on the world information pages.') !!}
                    <div>{!! Form::file('alt[image][]') !!}</div>
                    <div class="text-muted">Recommended size: 200px x 200px</div>
                </div>
            </div>
        </div>
    </div>

    <h3>Preview</h3>
    <div class="card mb-3">
        <div class="card-body">
            @include('world._feature_entry', ['feature' => $feature])
        </div>
    </div>
@endif

@endsection

@section('scripts')
@parent
<script>
$( document ).ready(function() {
    $('.delete-feature-button').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('admin/data/traits/delete') }}/{{ $feature->id }}", 'Delete Trait');
    });

    $('#add-type').on('click', function(e) {
            e.preventDefault();
            addTypeRow();
        });
        $('.remove-type').on('click', function(e) {
            e.preventDefault();
            removeTypeRow($(this));
        })
        function addTypeRow() {
            var $clone = $('.type-row').clone();
            $('#typeList').append($clone);
            $clone.removeClass('hide type-row');
            $clone.find('.remove-type').on('click', function(e) {
                e.preventDefault();
                removeTypeRow($(this));
            })
        }
        function removeTypeRow($trigger) {
            $trigger.parent().parent().parent().parent().remove();
        }
});

</script>
@endsection
