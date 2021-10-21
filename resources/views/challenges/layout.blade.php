@extends('layouts.app')

@section('title')
    Challenges ::
    @yield('challenges-title')
@endsection

@section('sidebar')
    @include('challenges._sidebar')
@endsection

@section('content')
    @yield('content')
@endsection

@section('scripts')
@parent
@endsection
