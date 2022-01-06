<li class="list-group-item">
    <a class="card-title h5 collapse-title"  data-toggle="collapse" href="#openRarityBoxForm"> Open Rarity Box</a>
    <div id="openRarityBoxForm" class="collapse">
        {!! Form::hidden('tag', $tag->tag) !!}
        <p>This item can be opened to obtain an item with the rarity <strong>{{ $tag->data }}</strong>. Please note that you can only select one item each time you open this box, so if you have multiple and want different items, you should open them one at a time. This action is not reversible. Are you sure you want to open this box?</p>
        <div class="form-group">
            {!! Form::label('item_id', 'Item') !!}
            {!! Form::select('item_id', App\Models\Item\Item::released()->whereNotNull('data')->where('data->rarity', $tag->data)->orderBy('name')->pluck('name','id')->toArray(), null, ['class' => 'form-control rarityBox selectize', 'placeholder' => 'Select an Item']) !!}
        </div>
        <script>
            $(document).ready(function() {
                $('.rarityBox').selectize();
            });
        </script>
        <div class="text-right">
            {!! Form::button('Open', ['class' => 'btn btn-primary', 'name' => 'action', 'value' => 'act', 'type' => 'submit']) !!}
        </div>
    </div>
</li>
