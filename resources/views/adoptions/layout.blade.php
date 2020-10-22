@extends('layouts.app')

@section('title') 
    Adoptions :: 
    @yield('adoptions-title')
@endsection

@section('sidebar')
    @include('adoptions._sidebar')
@endsection

@section('content')
    @yield('adoptions-content')
@endsection

@section('scripts')
@parent
@endsection