<div class="row world-entry">
    <div class="col-md-3 world-entry-image"><a href="{{ $frame->imageUrl }}" data-lightbox="entry" data-title="{{ $frame->name }}">
        <img src="{{ $frame->imageUrl }}" class="world-entry-image" alt="{{ $frame->name }}" />
    </a></div>
    <div class="{{ $frame->imageUrl ? 'col-md-9' : 'col-12' }}">
        <h3>
            {!! $frame->name !!} @if(isset($frame->searchUrl) && $frame->searchUrl) <a href="{{ $sframe->earchUrl }}" class="world-entry-search text-muted"><i class="fas fa-search"></i></a>  @endif
            @if($frame->is_default)
                <br/><small>Default Frame {!! add_help('This frame is automatically available to all characters, and is used by default.') !!}</small>
            @endif
        </h3>
        @if($frame->species_id)
            <div><strong>Species:</strong> {!! $frame->species->displayName !!} @if($frame->subtype_id) ({!! $frame->subtype->displayName !!} subtype) @endif</div>
        @endif
        <div class="world-entry-text parsed-text">
            {!! $frame->parsed_description !!}
        </div>
    </div>
</div>
