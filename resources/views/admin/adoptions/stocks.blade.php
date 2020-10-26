@extends('admin.layout')

@section('admin-title') Adoptions @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Adoptions' => 'admin/data/adoptions', 'Adoption Stock' => 'admin/data/adoptions/edit']) !!}

<h1>Adopts</h1>

<p>Press 'create adopt stock' to add to the center.</p> 
<p>Only characters owned by ''Admin'' (user where id = 1) can be added to the center.</p>
<p>To edit, press 'edit adoptable'.</p>

    @foreach($stock as $stocks)
        <div class="card mb-2">
            <div class="card-body row p-3">
                <div class="col col-form-label">
                    <strong><a href="{{ $stocks->character->url }}"> {!! $stocks->character->displayname !!}</a> (<a href="Species">{!! $stocks->character->image->species !!}</a>)</strong>
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
                    <a href="{{ url('admin/data/stock/edit/'.$stocks->id) }}" class="btn btn-dark">Edit Adoptable (Opens Modal)</a>
                </div>
            </div>
        </div>
        <a href="{{ url('admin/data/adoptions/stock/edit/1') }}" class="btn btn-primary">Create Adopt Stock</a>
@endsection