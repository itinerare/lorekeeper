@extends('challenges.layout')

@section('challenges-title') My Logs @endsection

@section('content')
{!! breadcrumbs(['Challenges' => 'challenges', 'My Logs' => 'challenges/my-challenges']) !!}

<h2>My Logs</h2>

<p>This is a list of your current and past challenge logs.</p>

<div>
    {!! Form::open(['method' => 'GET', 'class' => '']) !!}
        <div class="form-inline justify-content-end">
            <div class="form-group ml-3 mb-3">
                {!! Form::select('sort', [
                    'newest'               => 'Newest First',
                    'oldest'               => 'Oldest First'
                ], Request::get('sort') ? : 'category', ['class' => 'form-control']) !!}
            </div>
            <div class="form-group ml-3 mb-3">
                {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
            </div>
        </div>
    {!! Form::close() !!}
</div>

<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link {{ !Request::get('type') || Request::get('type') == 'active' ? 'active' : '' }}" href="{{ url('challenges/my-challenges') }}">Active</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ Request::get('type') == 'old' ? 'active' : '' }}" href="{{ url('challenges/my-challenges?type=old') }}">Old</a>
    </li>
</ul>

{!! $logs->render() !!}

<div class="row ml-md-2">
  <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-bottom">
    <div class="col-12 col-md-4 font-weight-bold">Challenge</div>
    <div class="col-6 col-md-3 font-weight-bold">Prompts Completed</div>
    <div class="col-6 col-md-2 font-weight-bold">Registered</div>
    <div class="col-6 col-md-1 font-weight-bold">Status</div>
  </div>

  @foreach($logs as $log)
    <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-top">
      <div class="col-12 col-md-4">{!! $log->challenge->displayName !!}</div>
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

{!! $logs->render() !!}

<div class="text-center mt-4 small text-muted">{{ $logs->total() }} result{{ $logs->total() == 1 ? '' : 's' }} found.</div>

@endsection
