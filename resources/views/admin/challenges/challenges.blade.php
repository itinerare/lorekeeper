@extends('admin.layout')

@section('admin-title') Challenge Logs @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Challenge Logs' => 'admin/challenges/active']) !!}

<h1>
    Challenge Logs
</h1>

<p>
    This is an index of all current and old user challenge logs. While these logs do not form a queue per se-- challenges are activated automatically and are submitted via the standard submissions flow, during which they must be accepted and marked "Old"-- they are presented here for informational purposes.
</p>

<ul class="nav nav-tabs mb-3">
  <li class="nav-item">
    <a class="nav-link {{ set_active('admin/challenges/active*') }} {{ set_active('admin/challenges') }}" href="{{ url('admin/challenges/active') }}">Active</a>
  </li>
  <li class="nav-item">
    <a class="nav-link {{ set_active('admin/challenges/old*') }}" href="{{ url('admin/challenges/old') }}">Old</a>
  </li>
</ul>

{!! $challenges->render() !!}

<div class="row ml-md-2">
    <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-bottom">
      <div class="col-12 col-md-2 font-weight-bold">Challenge</div>
      <div class="col-6 col-md-2 font-weight-bold">User</div>
      <div class="col-6 col-md-3 font-weight-bold">Prompts Completed</div>
      <div class="col-6 col-md-2 font-weight-bold">Registered</div>
      <div class="col-6 col-md-1 font-weight-bold">Status</div>
    </div>

    @foreach($challenges as $log)
      <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-top">
        <div class="col-12 col-md-2">{!! $log->challenge->displayName !!}</div>
        <div class="col-6 col-md-2">{!! $log->user->displayName !!}</div>
        <div class="col-6 col-md-3">
            {{ count((array) $log->data) }}/{{ count($log->challenge->data) }}
        </div>
        <div class="col-6 col-md-2">{!! pretty_date($log->created_at) !!}</div>
        <div class="col-3 col-md-1">
          <span class="btn btn-{{ $log->status == 'Active' ? 'success' : 'secondary' }} btn-sm py-0 px-1">{{ $log->status }}</span>
        </div>
        <div class="col-3 col-md text-right"><a href="{{ $log->url }}" class="btn btn-primary btn-sm py-0 px-1">Details</a></div>
      </div>
    @endforeach
  </div>

{!! $challenges->render() !!}
<div class="text-center mt-4 small text-muted">{{ $challenges->total() }} result{{ $challenges->total() == 1 ? '' : 's' }} found.</div>

@endsection
