@extends('admin.layout')

@section('admin-title') Status Effects @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Status Effects' => 'admin/data/status-effects']) !!}

<h1>Status Effects</h1>

<p>This is a list of status effects that can be applied to characters. Status effects can be distributed much like other rewards, including from prompts, loot tables, and so on.</p>

<div class="text-right mb-3"><a class="btn btn-primary" href="{{ url('admin/data/status-effects/create') }}"><i class="fas fa-plus"></i> Create New Status Effect</a></div>

{!! $statuses->render() !!}
  <div class="row ml-md-2">
    <div class="d-flex row flex-wrap col-12 pb-1 px-0 ubt-bottom">
      <div class="col-md-6 font-weight-bold">Name</div>
    </div>
    @foreach($statuses as $status)
    <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-top">
      <div class="col-md">{{ $status->name }}</div>
      <div class="col-md text-right"><a href="{{ url('admin/data/status-effects/edit/'.$status->id) }}" class="btn btn-primary">Edit</a></div>
    </div>
    @endforeach
  </div>
{!! $statuses->render() !!}
<div class="text-center mt-4 small text-muted">{{ $statuses->total() }} result{{ $statuses->total() == 1 ? '' : 's' }} found.</div>
@endsection
