@extends('home.layout')

@section('home-title')  Surrenders @endsection

@section('home-content')
    
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

{!! $surrender->render() !!}
<table>
    <thead>
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>User</th>
                    <th width="20%">Character</th>
                    <th width="20%">Submitted</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($surrender as $surrenders)
                    <tr>
                        <td>{!! $surrenders->user->displayName !!}</td>
                        <td class="text-break"><a href="{{ $surrenders->character->url }}">{!! $surrenders->character->displayname !!}</a></td>
                        <td>{!! format_date($surrenders->created_at) !!}</td>
                        <td>
                            <span class="badge badge-{{ $surrenders->status == 'Pending' ? 'secondary' : ($surrenders->status == 'Approved' ? 'success' : 'danger') }}">{{ $surrenders->status }}</span>
                        </td>
                        <td class="text-right"><a href="{{ $surrenders->viewUrl }}" class="btn btn-primary btn-sm">Details</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </thead>
</table>
{!! $surrender->render() !!}


@endsection