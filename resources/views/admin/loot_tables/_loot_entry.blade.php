<tr class="loot-row">
    <td>{!! Form::select('rewardable_type[]', Config::get('lorekeeper.extensions.item_entry_expansion.loot_tables.enable') ? ['Item' => 'Item', 'ItemRarity' => 'Item Rarity', 'Currency' => 'Currency', 'LootTable' => 'Loot Table', 'ItemCategory' => 'Item Category', 'ItemCategoryRarity' => 'Item Category (Conditional)', 'None' => 'None'] : ['Item' => 'Item', 'Currency' => 'Currency', 'LootTable' => 'Loot Table', 'ItemCategory' => 'Item Category', 'Status' => 'Status Effect (*Character Only)', 'None' => 'None'], $loot->rewardable_type, ['class' => 'form-control reward-type', 'placeholder' => 'Select Reward Type']) !!}</td>
    <td class="loot-row-select">
        @if($loot->rewardable_type == 'Item')
            {!! Form::select('rewardable_id[]', $items, $loot->rewardable_id, ['class' => 'form-control item-select selectize', 'placeholder' => 'Select Item']) !!}
        @elseif($loot->rewardable_type == 'ItemRarity')
            <div class="item-rarity-select d-flex">
                {!! Form::select('criteria[]', ['=' => '=', '<' => '<', '>' => '>', '<=' => '<=', '>=' => '>='], isset($loot->data['criteria']) ? $loot->data['criteria'] : null, ['class' => 'form-control', 'placeholder' => 'Criteria']) !!}
                {!! Form::select('rarity[]', $rarities, isset($loot->data['rarity']) ? $loot->data['rarity'] : null, ['class' => 'form-control', 'placeholder' => 'Rarity']) !!}
            </div>
        @elseif($loot->rewardable_type == 'Currency')
            {!! Form::select('rewardable_id[]', $currencies, $loot->rewardable_id, ['class' => 'form-control currency-select selectize', 'placeholder' => 'Select Currency']) !!}
        @elseif($loot->rewardable_type == 'Status')
            {!! Form::select('rewardable_id[]', $statuses, $loot->rewardable_id, ['class' => 'form-control status-select selectize', 'placeholder' => 'Select Status Effect']) !!}
        @elseif($loot->rewardable_type == 'LootTable')
            {!! Form::select('rewardable_id[]', $tables, $loot->rewardable_id, ['class' => 'form-control table-select selectize', 'placeholder' => 'Select Loot Table']) !!}
        @elseif($loot->rewardable_type == 'ItemCategoryRarity')
            <div class="category-rarity-select d-flex">
                {!! Form::select('rewardable_id[]', $categories, $loot->rewardable_id, ['class' => 'form-control selectize', 'placeholder' => 'Category']) !!}
                {!! Form::select('criteria[]', ['=' => '=', '<' => '<', '>' => '>', '<=' => '<=', '>=' => '>='], isset($loot->data['criteria']) ? $loot->data['criteria'] : null, ['class' => 'form-control', 'placeholder' => 'Criteria']) !!}
                {!! Form::select('rarity[]', $rarities, isset($loot->data['rarity']) ? $loot->data['rarity'] : null, ['class' => 'form-control', 'placeholder' => 'Rarity']) !!}
            </div>
        @elseif($loot->rewardable_type == 'ItemCategory')
            {!! Form::select('rewardable_id[]', $categories, $loot->rewardable_id, ['class' => 'form-control item-select selectize', 'placeholder' => 'Select Item']) !!}
        @elseif($loot->rewardable_type == 'None')
            {!! Form::select('rewardable_id[]', [1 => 'No reward given.'], $loot->rewardable_id, ['class' => 'form-control']) !!}
        @endif
    </td>
    <td>{!! Form::text('quantity[]', $loot->quantity, ['class' => 'form-control']) !!}</td>
    <td class="loot-row-weight">{!! Form::text('weight[]', $loot->weight, ['class' => 'form-control loot-weight']) !!}</td>
    {!! Form::hidden('subtable_id[]', $loot->subtable_id, ['class' => 'subtable-id']) !!}
    <td class="loot-row-chance"></td>
    <td class="text-right"><a href="#" class="btn btn-danger remove-loot-button">Remove</a></td>
</tr>
