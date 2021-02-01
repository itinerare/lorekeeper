<h3>Rarity</h3>

<p>This is the rarity of item that users will be able to select from when opening this item.</p>

<div class="form-group">
    {!! Form::select('rarity', $rarities, $tag->data, ['class' => 'form-control', 'placeholder' => 'Select a Rarity']) !!}
</div>
