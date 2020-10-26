@extends('admin.layout')

@section('admin-title') Adoptions @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Adoptions' => 'admin/data/adoptions',  'Edit Adoption' => 'admin/data/adoptions/edit/'.$adoption->id]) !!}

<h1>
    Edit Adoption
</h1>

{!! Form::open(['url' => 'admin/data/adoptions/edit/'.$adoption->id, 'files' => true]) !!}

<h3>Basic Information</h3>

<div class="form-group">
    {!! Form::label('Name') !!}
    {!! Form::text('name', $adoption->name, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('Adoption Image (Optional)') !!} {!! add_help('This image is used on the adoption index and on the adoption page as a header.') !!}
    <div>{!! Form::file('image') !!}</div>
    <div class="text-muted">Recommended size: None (Choose a standard size for all adoption images)</div>
    @if($adoption->has_image)
        <div class="form-check">
            {!! Form::checkbox('remove_image', 1, false, ['class' => 'form-check-input']) !!}
            {!! Form::label('remove_image', 'Remove current image', ['class' => 'form-check-label']) !!}
        </div>
    @endif
</div>

<div class="form-group">
    {!! Form::label('Description (Optional)') !!}
    {!! Form::textarea('description', $adoption->description, ['class' => 'form-control wysiwyg']) !!}
</div>

<div class="form-group">
    {!! Form::checkbox('is_active', 1, $adoption->id ? $adoption->is_active : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
    {!! Form::label('is_active', 'Set Active', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If turned off, the adoption will not be visible to regular users.') !!}
</div>

<div class="text-right">
    {!! Form::submit('Edit', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}

<h3>Current Adoption Stock</h3>
@foreach($adoption->stock as $stocks)
<div class="card mb-2">
    <div class="card-body row p-3">
        <div class="col col-form-label">
            <strong><a href="{{ $stocks->character->url }}"> {!! $stocks->character->displayname !!}</a> (<a href="Species">{!! $stocks->character->image->species->name !!}</a>)</strong>
        </div>
        <div class="col col-form-label">
            @foreach($stocks->currency as $currency)
            {!! $currency->cost !!}
            {!! $currency->currency->name !!}
            @endforeach
        </div>
        <div class="col col-form-label">
            @if($stocks->use_character_bank == 1)
            <i class="fas fa-paw" data-toggle="tooltip" title="Can be purchased using Character Bank"></i> 
            @endif
            @if($stocks->use_user_bank == 1) 
            <i class="fas fa-user" data-toggle="tooltip" title="Can be purchased using User Bank"></i> 
            @endif
        </div>
        <div class="col text-right">
            <a href="{{ url('admin/data/adoptions/stock/edit/'.$stocks->id) }}" class="btn btn-dark">Edit Adoptable</a>
        </div>
    </div>
</div>
@endforeach
@endsection