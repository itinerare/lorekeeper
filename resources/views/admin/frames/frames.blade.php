@extends('admin.layout')

@section('admin-title') Frames @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Frames' => 'admin/data/frames']) !!}

<h1>Frames</h1>

<p>This is a list of frames in the game. Frames exist in tandem with their paired item, via which they can be aquired by users and used to unlock the associated frame for characters.</p>

<div class="text-right mb-3">
    <a class="btn btn-primary" href="{{ url('admin/data/frame-categories') }}"><i class="fas fa-folder"></i> Frame Categories</a>
    <a class="btn btn-primary" href="{{ url('admin/data/frames/create') }}"><i class="fas fa-plus"></i> Create New Frame</a>
</div>

<div>
    {!! Form::open(['method' => 'GET', 'class' => 'form-inline justify-content-end']) !!}
        <div class="form-group mr-3 mb-3">
            {!! Form::text('name', Request::get('name'), ['class' => 'form-control', 'placeholder' => 'Name']) !!}
        </div>
        <div class="form-group mb-3">
            {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
        </div>
    {!! Form::close() !!}
</div>

@if(!count($frames))
    <p>No frames found.</p>
@else
    {!! $frames->render() !!}

        <div class="row ml-md-2 mb-4">
          <div class="d-flex row flex-wrap col-12 pb-1 px-0 ubt-bottom">
            <div class="col-5 col-md-6 font-weight-bold">Name</div>
          </div>
          @foreach($frames as $frame)
          <div class="d-flex row flex-wrap col-12 mt-1 pt-2 px-0 ubt-top">
            <div class="col-5 col-md-6"> {{ $frame->name }} </div>
            <div class="col-3 col-md-1 text-right">
              <a href="{{ url('admin/data/frames/edit/'.$frame->id) }}"  class="btn btn-primary py-0 px-2">Edit</a>
            </div>
          </div>
          @endforeach
        </div>

    {!! $frames->render() !!}
@endif

@endsection

@section('scripts')
@parent
@endsection
