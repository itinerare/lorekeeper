@extends('adoptions.layout')

@section('adoptions-title') {{ $adoption->name }} @endsection

@section('adoptions-content')
{!! breadcrumbs([$adoption->name => $adoption->url]) !!}

<h1>
    {{ $adoption->name }}
</h1>

<div class="text-center">
    <img src="{{ $adoption->adoptionImageUrl }}" />
    <p>{!! $adoption->parsed_description !!}</p>
</div>

@foreach($characters as $categoryId=>$categoryCharacters)
    <div class="card mb-3 inventory-category">
        <h5 class="card-header inventory-header">
            {!! isset($categories[$categoryId]) ? '<a href="'.$categories[$categoryId]->searchUrl.'">'.$categories[$categoryId]->name.'</a>' : 'Miscellaneous' !!}
        </h5>
        <div class="card-body inventory-body">
            @foreach($categoryCharacters->chunk(4) as $chunk)
                <div class="row mb-3">
                    @foreach($chunk as $character) 
                        <div class="col-sm-3 col-6 text-center inventory-character" data-id="{{ $character->pivot->id }}">
                            <div class="mb-1">
                                <a href="{{ $character->url }}"><img src="{{ $character->image->thumbnailUrl }}" class="img-thumbnail" /></a>
                            </div>
                            <div>
                                <a href="#" class="inventory-stack inventory-stack-name"><strong>{{ $character->slug }}</strong></a>
                                <div><strong>Cost: </strong> {!! $currencies[$character->pivot->currency_id]->display($character->pivot->cost) !!}</div>
                                @if($character->pivot->is_limited_stock == 0) <div>Stock: {{ $character->pivot->quantity }}</div> @endif
                                @if($character->pivot->purchase_limit) <div class="text-danger">Max {{ $character->pivot->purchase_limit }} per user</div> @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
@endforeach

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