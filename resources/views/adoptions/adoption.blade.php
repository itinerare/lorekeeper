@extends('layouts.app')

@section('title') {{ $adoption->name }} @endsection

@section('content')
{!! breadcrumbs([$adoption->name => $adoption->url]) !!}

<h1>
    {{ $adoption->name }}
</h1>

<div class="text-center">
    <img src="{{ $adoption->adoptionImageUrl }}" />
    <p>{!! $adoption->parsed_description !!}</p>
</div>

@if(!count($stock))
    <p>No stock found.</p>
@else 
<div class="card mb-3 inventory-category">
    @foreach($stock as $stocks)
    <div class="card">
        <h5 class="card-header">
                    <strong><a href="{{ $stocks->character->url }}"> {!! $stocks->character->displayname !!}</a> (<a href="{{ $stocks->character->image->species->url }}">{!! $stocks->character->image->species->name !!}</a>)</strong>
                </div>
            </h5>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="text-center" data-id="{{ $stocks->character->id }}">
                        <div class="mb-1">
                            <img src="{{ $stocks->character->image->thumbnailUrl }}">
                        </div>
                            <a href="#" class="inventory-stack inventory-stack-name"><strong>{{ $stocks->character->slug }}</strong></a>
                            <br>
                            <strong>Cost:</strong>
                    @if($stocks->currency->count() > 1)
                        <?php 
                            $currencies = []; // Create an empty array
                            foreach($stocks->currency as $currency)
                            {
                            $d1 = $currency->cost;
                            $d2 = $currency->currency->name;
                            $currencies[] = ' ' . $d1 . ' ' . $d2; // Add a new value to your array
                            }
                            echo implode(" or", $currencies); // implode the full array and separate the values with "or"
                        ?>
                            <br>
                        @else
                            @foreach($stocks->currency as $currency)
                            {!! $currency->cost !!}
                            {!! $currency->currency->name !!}
                            <br>
                        @endforeach
                    @endif
                </div>
                <div class="col col-form-label">
                    @if($stocks->use_character_bank == 1) <i class="fas fa-paw" data-toggle="tooltip" title="Can be purchased using Character Bank"></i>@endif
                    @if($stocks->use_user_bank == 1) <i class="fas fa-user" data-toggle="tooltip" title="Can be purchased using User Bank"></i> @endif
                </div>
            </div>
        </div>
    @endforeach
@endif

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.inventory-character').on('click', function(e) {
            e.preventDefault();
            
            loadModal("{{ url('adoptions/'.$adoption->id) }}/" + $(this).data('id'), 'Purchase Character');
        });
    });

</script>
@endsection