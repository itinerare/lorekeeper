<p>
    Enter the frame that this item will grant to the character selected by the user when using this item from their inventory. Note that the any default frames cannot be selected.
</p>

<div class="form-group">
    {!! Form::label('Frame') !!}
    {!! Form::select('frame_id', $frames, $tag->data, ['class' => 'form-control', 'placeholder' => 'Select a Frame', 'required', 'id' => 'select-frame']) !!}
</div>
