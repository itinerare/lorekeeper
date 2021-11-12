<li class="list-group-item">
    <a class="card-title h5 collapse-title"  data-toggle="collapse" href="#frameForm"> Unlock Frame</a>
    <div id="frameForm" class="collapse">
        {!! Form::hidden('tag', $tag->tag) !!}
        @php $frame = \App\Models\Frame\Frame::where('id', $tag->data)->first(); @endphp
        @if($frame)
            <p>This will unlock the {!! $frame->category ? $frame->category->displayName.' ' : '' !!}frame {!! $frame->displayName !!} for the selected character. This action is not reversible, and can only be performed once per character. Are you sure you want to unlock this frame?</p>
            <div class="form-group">
                {!! Form::select('frame_character_id', $characterOptions, null, ['class' => 'form-control mr-2 default character-select', 'placeholder' => 'Select Character']) !!}
            </div>
            <div class="text-right">
                {!! Form::button('Unlock Frame', ['class' => 'btn btn-primary', 'name' => 'action', 'value' => 'act', 'type' => 'submit']) !!}
            </div>
        @else
            <p>Something has gone wrong! There doesn't seem to be a valid frame here to unlock.</p>
        @endif
    </div>
</li>
