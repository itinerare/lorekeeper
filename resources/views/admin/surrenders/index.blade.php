@extends('admin.layout')

@section('admin-title') Surrender Queue @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Surrender Queue' => 'admin/surrender/pending']) !!}

<h1>
    Surrender Queue
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
    <a class="nav-link {{ set_active('admin/surrenders/pending*') }} {{ set_active('admin/surrenders/surrenders') }}" href="{{ url('admin/surrenders/pending') }}">Pending</a>
  </li>
  <li class="nav-item">
    <a class="nav-link {{ set_active('admin/surrenders/approved*') }}" href="{{ url('admin/surrenders/approved') }}">Approved</a>
  </li>
  <li class="nav-item">
    <a class="nav-link {{ set_active('admin/surrenders/rejected*') }}" href="{{ url('admin/surrenders/rejected') }}">Rejected</a>
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
                        <td class="text-right"><a href="{{ $surrenders->adminUrl }}" class="btn btn-primary btn-sm">Details</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </thead>
</table>
{!! $surrender->render() !!}


@endsection