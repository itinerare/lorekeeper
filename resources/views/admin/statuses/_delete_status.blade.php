@if($status)
    {!! Form::open(['url' => 'admin/data/status-effects/delete/'.$status->id]) !!}

    <p>You are about to delete the status effect <strong>{{ $status->name }}</strong>. This is not reversible. If characters with this status effect exist, you will not be able to delete this status effect.</p>
    <p>Are you sure you want to delete <strong>{{ $status->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Status Effect', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid status effect selected.
@endif
