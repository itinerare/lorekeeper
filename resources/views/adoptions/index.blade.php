@extends('adoptions.layout')

@section('adoptions-title') Adoption Index @endsection

@section('adoptions-content')
{!! breadcrumbs(['Adoptions' => 'adoptions']) !!}

<h1>
    Adoptions
</h1>

<div class="row adoptions-row">
    @foreach($adoptions as $adoption)
        <div class="col-md-3 col-6 mb-3 text-center">
            <div class="adoption-image">
                <a href="{{ $adoption->url }}"><img src="{{ $adoption->adoptionImageUrl }}" /></a>
            </div>
            <div class="adoption-name mt-1">
                <a href="{{ $adoption->url }}" class="h5 mb-0">{{ $adoption->name }}</a>
            </div>
        </div>
    @endforeach
</div>

@endsection
