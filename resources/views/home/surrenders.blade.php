@extends('home.layout')

@section('home-title')  Surrenders @endsection

@section('home-content')
    
{!! breadcrumbs(['Surrenders' => 'surrenders']) !!}
<h1>
    Surrenders
</h1>

{!! Form::open(['method' => 'GET', 'class' => 'form-inline justify-content-end']) !!}
    <div class="form-inline justify-content-end">
        <div class="form-group ml-3 mb-3">
            {!! Form::select('sort', [
                'newest'         => 'Newest First',
                'oldest'         => 'Oldest First',
            ], Request::get('sort') ? : 'oldest', ['class' => 'form-control']) !!}
        </div>
        <div class="form-group ml-3 mb-3">
            {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
        </div>
    </div>
{!! Form::close() !!}

<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link {{ !Request::get('type') || Request::get('type') == 'pending' ? 'active' : '' }}" href="{{ url('surrenders') }}">Pending</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ Request::get('type') == 'approved' ? 'active' : '' }}" href="{{ url('surrenders?type=approved') }}">Approved</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ Request::get('type') == 'rejected' ? 'active' : '' }}" href="{{ url('surrenders?type=rejected') }}">Rejected</a>
    </li>
</ul>

{!! $surrenders->render() !!}

<div class="row ml-md-2 mb-4">
    <div class="d-flex row flex-wrap col-12 pb-1 px-0 ubt-bottom">
        <div class="col-12 col-md-3 font-weight-bold">User</div>
        <div class="col-6 col-md-3 font-weight-bold">Character</div>
        <div class="col-6 col-md-3 font-weight-bold">Submitted</div>
        <div class="col-6 col-md font-weight-bold">Status</div>
    </div>
    @foreach($surrenders as $surrender)
    <div class="d-flex row flex-wrap col-12 mt-1 pt-2 px-0 ubt-top">
        <div class="col-12 col-md-3">{!! $surrender->user->displayName !!}</div>
        <div class="col-6 col-md-3"><a href="{{ $surrender->character->url }}">{!! $surrender->character->displayname !!}</a></div>
        <div class="col-6 col-md-3">{!! pretty_date($surrender->created_at) !!}</div>
        <div class="col-6 col-md-2"><span class="badge badge-{{ $surrender->status == 'Pending' ? 'secondary' : ($surrender->status == 'Approved' ? 'success' : 'danger') }}">{{ $surrender->status }}</span></div>
        <div class="col-6 col-md"><a href="{{ $surrender->viewUrl }}" class="btn btn-primary btn-sm">Details</a></div>
    </div>
    @endforeach
</div>

{!! $surrenders->render() !!}


@endsection