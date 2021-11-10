@if($frame)
    {!! Form::open(['url' => 'admin/data/frames/delete/'.$frame->id]) !!}

    <p>You are about to delete the frame <strong>{{ $frame->name }}</strong>. This is not reversible. If this frame exists in at least one character's possession, you will not be able to delete this frame. Consider making its corresponding item unavailable instead.</p>
    <p>Are you sure you want to delete <strong>{{ $frame->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Frame', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid frame selected.
@endif
