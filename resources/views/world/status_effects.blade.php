@extends('world.layout')

@section('title') Status Effects @endsection

@section('content')
{!! breadcrumbs(['World' => 'world', 'Status Effects' => 'world/status-effects']) !!}
<h1>Status Effects</h1>

<div>
    {!! Form::open(['method' => 'GET', 'class' => 'form-inline justify-content-end']) !!}
        <div class="form-group mr-3 mb-3">
            {!! Form::text('name', Request::get('name'), ['class' => 'form-control']) !!}
        </div>
        <div class="form-group mb-3">
            {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
        </div>
    {!! Form::close() !!}
</div>

{!! $statuses->render() !!}
@foreach($statuses as $status)
    <div class="card mb-3">
        <div class="card-body">
            @include('world._entry', ['imageUrl' => $status->imageUrl, 'name' => $status->displayName, 'description' => $status->parsed_description])
        </div>
    </div>
@endforeach
{!! $statuses->render() !!}

<div class="text-center mt-4 small text-muted">{{ $statuses->total() }} result{{ $statuses->total() == 1 ? '' : 's' }} found.</div>

@endsection
