@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title') {{ $character->fullName }}'s Status Effects @endsection

@section('meta-img') {{ $character->image->thumbnailUrl }} @endsection

@section('profile-content')
{!! breadcrumbs([($character->category->masterlist_sub_id ? $character->category->sublist->name.' Masterlist' : 'Character masterlist') => ($character->category->masterlist_sub_id ? 'sublist/'.$character->category->sublist->key : 'masterlist' ), $character->fullName => $character->url, "Status Effects" => $character->url.'/status-effects']) !!}

@include('character._header', ['character' => $character])

<h3>
    @if(Auth::check() && Auth::user()->hasPower('edit_inventories'))
        <a href="#" class="float-right btn btn-outline-info btn-sm" id="grantButton" data-toggle="modal" data-target="#grantModal"><i class="fas fa-cog"></i> Admin</a>
    @endif
    Status Effects
</h3>
@if(count($statuses))
<div class="card mb-4">
    <ul class="list-group list-group-flush">

        @foreach($statuses as $status)
            <li class="list-group-item">
                <div class="row">
                    <div class="col-lg-2 col-md-3 col-6 text-right">
                        <strong>
                            <a href="{{ $status->url }}">
                                {!! $status->displaySeverity($status->quantity) !!}
                            </a>
                        </strong>
                    </div>
                    <div class="col-lg-10 col-md-9 col-6">
                        {{ $status->quantity }}
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
</div>
@else
<div class="card mb-4">
    <div class="card-body">
        No current status effects.
    </div>
</div>
@endif

<h3>Latest Activity</h3>
<div class="row ml-md-2">
    <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-bottom">
        <div class="col-6 col-md-2 font-weight-bold">Sender</div>
        <div class="col-6 col-md-2 font-weight-bold">Recipient</div>
        <div class="col-6 col-md-2 font-weight-bold">Status Effect</div>
        <div class="col-6 col-md-4 font-weight-bold">Log</div>
        <div class="col-6 col-md-2 font-weight-bold">Date</div>
    </div>
    @foreach($logs as $log)
        @include('character._status_log_row', ['log' => $log, 'owner' => $character])
    @endforeach
</div>
<div class="text-right">
    <a href="{{ url($character->url.'/status-effect-logs') }}">View all...</a>
</div>

@if(Auth::check() && Auth::user()->hasPower('edit_inventories'))
    <div class="modal fade" id="grantModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="modal-title h5 mb-0">[ADMIN] Grant/remove status effect</span>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    {!! Form::open(['url' => 'admin/character/'.$character->slug.'/grant-status']) !!}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('status_id', 'Status Effect') !!}
                                    {!! Form::select('status_id', $statusOptions, null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('quantity', 'Quantity') !!} {!! add_help('If the value given is less than 0, this will be deducted from the character.') !!}
                                    {!! Form::text('quantity', null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('data', 'Reason (Optional)') !!} {!! add_help('A reason for the grant. This will be noted in the logs.') !!}
                            {!! Form::text('data', null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="text-right">
                            {!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endif

@endsection
