@extends('adoptions.layout')

@section('adoptions-title') My Purchase History @endsection

@section('adoptions-content')
{!! breadcrumbs(['Adoptions' => 'adoptions', 'My Purchase History' => 'history']) !!}

<h1>
    My Purchase History
</h1>

{!! $logs->render() !!}
<table class="table table-sm">
    <thead>
        <th>Character</th>
        <th>Adoption</th>
        <th>Cost</th>
        <th>Date</th>
    </thead>
    <tbody>
        @foreach($logs as $log)
            @include('adoptions._purchase_history_row', ['log' => $log])
        @endforeach
    </tbody>
</table>
{!! $logs->render() !!}

@endsection