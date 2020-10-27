@extends('admin.layout')

@section('admin-title') Adopts @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Adoptions' => 'admin/data/adoptions', 'Adoption Stock' => 'admin/data/stock']) !!}

<h1>Adopts</h1>

<p>Press 'create adopt stock' to add to the center.</p> 
<p>Only characters owned by ''Admin'' (user where id = 1) can be added to the center.</p>
<p>To edit, press 'edit adoptable'.</p>

<a href="{{ url('admin/data/stock/create') }}" class="text-right btn btn-primary mb-2">Create Adopt Stock</a>

@if(!count($stock))
    <p>No stock found.</p>
@else 
    @foreach($stock as $stocks)
        <div class="card mb-2">
            <div class="card-body row p-3">
                <div class="col col-form-label">
                    <strong>Adopt Stock #{{ $stocks->id }}<a href="{{ $stocks->character->url }}"> {!! $stocks->character->displayname !!}</a> (<a href="Species">{!! $stocks->character->image->species->name !!}</a>)</strong>
                </div>
                <div class="col col-form-label">
                    @if($stocks->currency == '[]') No cost added
                    @else
                    @foreach($stocks->currency as $currency)
                    {!! $currency->cost !!}
                    {!! $currency->currency->name !!},
                    @endforeach
                    @endif
                </div>
                <div class="col col-form-label">
                    @if($stocks->use_character_bank == 1) <i class="fas fa-paw" data-toggle="tooltip" title="Can be purchased using Character Bank"></i>@endif
                    @if($stocks->use_user_bank == 1) <i class="fas fa-user" data-toggle="tooltip" title="Can be purchased using User Bank"></i> @endif
                </div>
                <div class="col text-right">
                    <a href="{{ url('admin/data/stock/edit/'.$stocks->id) }}" class="btn btn-dark edit-button">Edit Adoptable</a>
                </div>
            </div>
        </div>
        @endforeach
    @endif
@endsection

@section('scripts')
@parent
<script>
//$( document ).ready(function() {    
    //$('.edit-button').on('click', function(e) {
        //e.preventDefault();
        //loadModal("{{ url('admin/data/adoptions/stock/edit/'.$stocks->id) }}", 'Edit Stock');
    //});
//});
    
</script>
@endsection