@if($type == 1)
{!! Form::label('Subtype (Optional)') !!}
{!! Form::select('subtype_id', $subtypes, old('subtype_id') ? : $subtype, ['class' => 'form-control', 'id' => 'subtype']) !!}
@elseif($type == 2)
{!! Form::label('Subtype Two (Optional)') !!}
{!! Form::select('subtype_id_2', $subtypes, old('subtype_id_2') ? : $subtype, ['class' => 'form-control', 'id' => 'subtype_2']) !!}
@endif
