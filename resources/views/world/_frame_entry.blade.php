<div class="row world-entry">
    @if($imageUrl)
        <div class="col-md-3 world-entry-image"><a href="{{ $imageUrl }}" data-lightbox="entry" data-title="{{ $name }}">
            <img src="{{ $imageUrl }}" class="world-entry-image" alt="{{ $name }}" />
        </a></div>
    @endif
    <div class="{{ $imageUrl ? 'col-md-9' : 'col-12' }}">
        <h3>
            {!! $name !!} @if(isset($searchUrl) && $searchUrl) <a href="{{ $searchUrl }}" class="world-entry-search text-muted"><i class="fas fa-search"></i></a>  @endif
            @if($isDefault)
                <br/><small>Default Frame {!! add_help('This frame is automatically available to all characters, and is used by default.') !!}</small>
            @endif
        </h3>
        <div class="world-entry-text">
            {!! $description !!}
        </div>
    </div>
</div>
