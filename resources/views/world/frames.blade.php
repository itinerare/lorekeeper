@extends('world.layout')

@section('title') Frames @endsection

@section('content')
{!! breadcrumbs(['World' => 'world', 'Frames' => 'world/frames']) !!}
<h1>Frames</h1>

<div>
    {!! Form::open(['method' => 'GET', 'class' => '']) !!}
        <div class="form-inline justify-content-end">
            <div class="form-group ml-3 mb-3">
                {!! Form::text('name', Request::get('name'), ['class' => 'form-control', 'placeholder' => 'Name']) !!}
            </div>
            <div class="form-group ml-3 mb-3">
                {!! Form::select('species_id', $specieses, Request::get('species_id'), ['class' => 'form-control']) !!}
            </div>
            <div class="form-group ml-3 mb-3">
                {!! Form::select('frame_category_id', $categories, Request::get('name'), ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="form-inline justify-content-end">
            <div class="form-group ml-3 mb-3">
                {!! Form::select('sort', [
                    'alpha'          => 'Sort Alphabetically (A-Z)',
                    'alpha-reverse'  => 'Sort Alphabetically (Z-A)',
                    'category'       => 'Sort by Category',
                    'species'        => 'Sort by Species',
                    'newest'         => 'Newest First',
                    'oldest'         => 'Oldest First'
                ], Request::get('sort') ? : 'category', ['class' => 'form-control']) !!}
            </div>
            <div class="form-group ml-3 mb-3">
                {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
            </div>
        </div>
    {!! Form::close() !!}
</div>

{!! $frames->render() !!}
@foreach($frames as $frame)
    <div class="card mb-3">
        <div class="card-body">
        @include('world._frame_entry', ['frame' => $frame])
        </div>
    </div>
@endforeach
{!! $frames->render() !!}

<div class="text-center mt-4 small text-muted">{{ $frames->total() }} result{{ $frames->total() == 1 ? '' : 's' }} found.</div>

@endsection
