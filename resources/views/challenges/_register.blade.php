@if($challenge)
    {!! Form::open(['url' => 'challenges/new/'.$challenge->id]) !!}

    <p>This will register you for the <strong>{{ $challenge->name }}</strong> challenge. You can take on {{ Settings::get('challenges_concurrent').' challenge'.(Settings::get('challenges_concurrent') == 1 ? '' : 's') }} at once. Are you sure you want to register for this challenge?</p>

    <div class="text-right">
        {!! Form::submit('Register', ['class' => 'btn btn-success']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid challenge selected.
@endif
