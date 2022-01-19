<h3>Status Effect</h3>

<p>This is the status effect and quantity that will be removed from a character selected by the user when they use the item from their inventory. The quantity entered should be a negative number, and will be adjusted once submitted if necessary.</p>

<div class="input-group mb-4">
    <div class="input-group-prepend">
        <span class="input-group-text">Status Effect</span>
    </div>
    {!! Form::select('status_effect_id', $statuses, $tag->getData() ? $tag->getData()['status_effect_id'] : null, ['class' => 'form-control', 'aria-label' => 'Status Effect', 'placeholder' => 'Select Status Effect']) !!}
    {!! Form::number('quantity', $tag->getData() ? $tag->getData()['quantity'] : 1, ['class' => 'form-control', 'aria-label' => 'Quantity']) !!}
    <div class="input-group-append">
        <span class="input-group-text">Quantity</span>
    </div>
  </div>
