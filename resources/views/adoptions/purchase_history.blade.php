@extends('adoptions.layout')

@section('adoptions-title') My Purchase History @endsection

@section('adoptions-content')
{!! breadcrumbs([$adoption->name => 'adoptions', 'My Purchase History' => 'history']) !!}

<h1>
    My Purchase History
</h1>

{!! $logs->render() !!}
    <div class="row ml-md-2 mb-4">
        <div class="d-flex row flex-wrap col-12 pb-1 px-0 ubt-bottom">
            <div class="col-12 col-md-5 font-weight-bold">Character</div>
            <div class="col-6 col-md-4 font-weight-bold">Cost</div>
            <div class="col-6 col-md font-weight-bold">Date</div>
        </div>
        @foreach($logs as $log)
        <div class="d-flex row flex-wrap col-12 mt-1 pt-2 px-0 ubt-top">
            <div class="col-12 col-md-5">{!! $log->adopt ? $log->adopt->displayName : '(Deleted Character)' !!}</div>
            <div class="col-6 col-md-4">{!! $log->currency ? $log->currency->display($log->cost) : $log->cost . ' (Deleted Currency)' !!}</div>
            <div class="col-6 col-md">{!! format_date($log->created_at) !!}</div>
        </div>
        @endforeach
    </div>
{!! $logs->render() !!}

@endsection