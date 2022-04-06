@extends('adoptions.layout')

@section('title') Surrender Character @endsection

@section('content')
{!! breadcrumbs([$adoption->name => 'adoptions', 'Surrender' => 'surrender']) !!}
<h1>
    Surrender Character
</h1>
@if(!Settings::get('is_surrenders_open'))
<div class="alert alert-danger">Surrenders are currently closed</div>
@else
<div class="alert alert-warning">Please note that by surrendering your characters you acknowledge they will be sold for onsite currency and retrieval after the form has been approved may not be possible</div>

{!! Form::open(['url' => 'surrenders/new/post']) !!}

<div class="card mb-3 stock">
    <div class="card-body">
        <div class="form-group">
            {!! Form::label('character_id', 'Character') !!}
            {!! Form::select('character_id', $characters, null, ['class' => 'form-control stock-field', 'data-name' => 'character_id']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('notes', 'Additional Notes (optional)') !!}
            {!! Form::textarea('notes', null, ['class' => 'form-control', 'placeholder' => 'Include any extra neccessary details, as well as any character pages such as Toyhouse etc.']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('worth', 'Suggested Worth (optional)') !!}
            {!! Form::number('worth', null, ['class' => 'form-control', 'placeholder' => 'Suggested worth does not influence amount of currency given, but gives admins insight to see if the grant has malfunctioned.']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('currency_id', 'Currency') !!} {!! add_help('If you do not want to input a worth, just leave this field as default.') !!}
            {!! Form::select('currency_id', $currencies, null , ['class' => 'form-control', 'placeholder' => 'Select Currency type']) !!}
        </div>
        <div class="text-right">
            {!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}
        </div>
    </div>
</div>
{!! Form::close() !!}
@endif
@endsection
