<li class="list-group-item">
    <a class="card-title h5 collapse-title"  data-toggle="collapse" href="#buffForm"> Apply Buff{{ $tag->getData()['quantity'] == 1 ? '' : 's' }}</a>
    <div id="buffForm" class="collapse">
        {!! Form::hidden('tag', $tag->tag) !!}
        <p>This will apply the {!! \app\Models\Status\StatusEffect::where('id', $tag->getData()['status_effect_id'])->first() ? \app\Models\Status\StatusEffect::where('id', $tag->getData()['status_effect_id'])->first()->displayName : '(Deleted Status)' !!} status effect x{{ $tag->getData()['quantity'] }} to the selected character for each {{ $item->name }} used. This action is not reversible. Are you sure you want to apply this buff?</p>
        <div class="form-group">
            {!! Form::select('buff_character_id', $characterOptions, null, ['class' => 'form-control mr-2 default character-select', 'placeholder' => 'Select Character']) !!}
        </div>
        <div class="text-right">
            {!! Form::button('Use', ['class' => 'btn btn-primary', 'name' => 'action', 'value' => 'act', 'type' => 'submit']) !!}
        </div>
    </div>
</li>
