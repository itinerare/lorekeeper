<div class="row world-entry">
    @if($feature->has_image)
        <div class="col-md-3 world-entry-image"><a href="{{ $feature->imageUrl }}" data-lightbox="entry" data-title="{{ $feature->name }}"><img src="{{ $feature->imageUrl }}" class="world-entry-image" alt="{{ $feature->name }}" /></a></div>
    @endif
    <div class="{{ $feature->has_image ? 'col-md-9' : 'col-12' }}">
        <h3>{!! $feature->displayName !!} <a href="{{ $feature->searchUrl }}" class="world-entry-search text-muted"><i class="fas fa-search"></i></a></h3>
        @if($feature->feature_category_id)
            <div><strong>Category:</strong> {!! $feature->category->displayName !!}</div>
        @endif
        @if($feature->parent_id && $feature->parent->is_visible)
            <div><strong>Parent Trait:</strong> {!! $feature->parent->displayName !!}</div>
        @endif
        @if($feature->species_id)
            <div><strong>Species:</strong> {!! $feature->species->displayName !!} @if($feature->subtype_id) ({!! $feature->subtype->displayName !!} subtype) @endif</div>
        @endif
        <div class="world-entry-text parsed-text">
            {!! $feature->parsed_description !!}
        </div>
        @if($feature->altTypes()->visible()->get()->count())
            <h5 class="inventory-header">
                Alternate Types
                <a class="small collapse-toggle collapsed" href="#alt-{{ $feature->id }}" data-toggle="collapse">Show</a></h3>
            </h5>
            <div class="collapse show" id="alt-{{ $feature->id }}">
                <ul>
                    @foreach($feature->altTypes()->visible()->get() as $altType)
                        <li>
                            {!! $altType->displayName !!} <a href="{{ $altType->searchUrl }}" class="world-entry-search text-muted"><i class="fas fa-search"></i></a>
                            @if(!$altType->display_separately)
                                @if($altType->species_id)
                                    <div><strong>Species:</strong> {!! $altType->species->displayName !!} @if($altType->subtype_id) ({!! $altType->subtype->displayName !!} subtype) @endif</div>
                                @endif
                                <div class="world-entry-text parsed-text">
                                    {!! $altType->parsed_description !!}
                                </div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>
