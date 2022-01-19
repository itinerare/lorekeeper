<li class="list-group-item">
    <a class="card-title h5 collapse-title"  data-toggle="collapse" href="#cureForm"> Cure Status</a>
    <div id="cureForm" class="collapse">
        {!! Form::hidden('tag', $tag->tag) !!}
        <p>This will remove the {!! \app\Models\Status\StatusEffect::where('id', $tag->getData()['status_effect_id'])->first() ? \app\Models\Status\StatusEffect::where('id', $tag->getData()['status_effect_id'])->first()->displayName : '(Deleted Status)' !!} status effect x{{ $tag->getData()['quantity'] }} for the selected character for each {{ $item->name }} used. This action is not reversible. Are you sure you want to cure this status effect?</p>
        <div class="form-group">
            {!! Form::select('cure_character_id', $characterOptions, null, ['class' => 'form-control mr-2 default character-select', 'placeholder' => 'Select Character']) !!}
        </div>
        <div class="text-right">
            {!! Form::button('Use', ['class' => 'btn btn-primary', 'name' => 'action', 'value' => 'act', 'type' => 'submit']) !!}
        </div>
    </div>
</li>
