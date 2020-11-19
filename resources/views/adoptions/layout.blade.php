@extends('layouts.app')

@section('title') 
    Adoptions :: 
    @yield('adoptions-title')
@endsection

@section('sidebar')
    @include('adoptions._sidebar',['name' => $adoption->name])
@endsection

@section('content')
    @yield('adoptions-content')
@endsection

@section('scripts')
@parent
@endsection