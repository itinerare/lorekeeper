@if($challenge)
    {!! Form::open(['url' => 'admin/data/challenges/delete/'.$challenge->id]) !!}

    <p>You are about to delete the challenge <strong>{{ $challenge->name }}</strong>. This is not reversible. If challenge logs exist under this challenge, you will not be able to delete it.</p>
    <p>Are you sure you want to delete <strong>{{ $challenge->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Challenge', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid challenge selected.
@endif
