@extends('admin.layout')

@section('admin-title') Challenges @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Challenges' => 'admin/data/challenges']) !!}

<h1>Challenges</h1>

<p>This is a list of challenges users can register for. Challenges consist of a series of prompts (not to be confused with <a href="{{ url('admin/data/prompts') }}">Prompts</a>) that users can start at any time (if active) and complete at their leisure. Once completed, challenges can be submitted for rewards via the standard submission system (a button is provided to auto-fill the prompt ID and URL field appropriately).</p>

<div class="text-right mb-3">
    <a class="btn btn-primary" href="{{ url('admin/data/challenges/create') }}"><i class="fas fa-plus"></i> Create New Challenge</a>
</div>

@if(!count($challenges))
    <p>No challenges found.</p>
@else
    {!! $challenges->render() !!}

    <div class="row ml-md-2">
      <div class="d-flex row flex-wrap col-12 pb-1 px-0 ubt-bottom">
        <div class="col-4 col-md-1 font-weight-bold">Active</div>
        <div class="col-4 col-md-6 font-weight-bold">Name</div>
      </div>
      @foreach($challenges as $challenge)
      <div class="d-flex row flex-wrap col-12 mt-1 pt-2 px-0 ubt-top">
        <div class="col-2 col-md-1">
          {!! $challenge->is_active ? '<i class="text-success fas fa-check"></i>' : '' !!}
        </div>
        <div class="col-4 col-md-6 text-truncate">
          {{ $challenge->name }}
        </div>
        <div class="col-3 col-md text-right">
          <a href="{{ url('admin/data/challenges/edit/'.$challenge->id) }}"  class="btn btn-primary py-0 px-2">Edit</a>
        </div>
      </div>
      @endforeach
    </div>

    {!! $challenges->render() !!}

    <div class="text-center mt-4 small text-muted">{{ $challenges->total() }} result{{ $challenges->total() == 1 ? '' : 's' }} found.</div>
@endif

@endsection
