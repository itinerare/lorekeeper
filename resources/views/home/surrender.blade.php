@extends('user.layout')

@section('profile-title') Surrender (#{{ $surrender->id }}) @endsection

@section('profile-content')
{!! breadcrumbs(['Users' => 'users', $user->name => $user->url, 'Surrender (#' . $surrender->id . ')' => $surrender->viewUrl]) !!}

@include('home._surrender_user_content', ['surrender' => $surrender])

@endsection