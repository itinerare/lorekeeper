<h1>
    Surrender (#{{ $surrender->id }})
    <span class="float-right badge badge-{{ $surrender->status == 'Pending' ? 'secondary' : ($surrender->status == 'Approved' ? 'success' : 'danger') }}">{{ $surrender->status }}</span>
</h1>

<div class="mb-1">
    <div class="row">
        <div class="col-md-2 col-4"><h5>User</h5></div>
        <div class="col-md-10 col-8">{!! $surrender->user->displayName !!}</div>
    </div>
    <div class="row">
        <div class="col-md-2 col-4"><h5>URL</h5></div>
        <div class="col-md-10 col-8"><a href="{{ $surrender->url }}">{{ $surrender->url }}</a></div>
    </div>
    <div class="row">
        <div class="col-md-2 col-4"><h5>Submitted</h5></div>
        <div class="col-md-10 col-8">{!! format_date($surrender->created_at) !!} ({{ $surrender->created_at->diffForHumans() }})</div>
    </div>
    @if($surrender->status != 'Pending')
        <div class="row">
            <div class="col-md-2 col-4"><h5>Processed</h5></div>
            <div class="col-md-10 col-8">{!! format_date($surrender->updated_at) !!} ({{ $surrender->updated_at->diffForHumans() }}) by {!! $surrender->staff->displayName !!}</div>
        </div>
    @endif
</div>
<h2>Comments</h2>
<div class="card mb-3"><div class="card-body">{!! nl2br(htmlentities($surrender->notes)) !!}</div></div>
@if(Auth::check() && $surrender->staff_comments && ($surrender->user_id == Auth::user()->id || Auth::user()->hasPower('manage_surrenders')))
    <h2>Staff Comments</h2>
    <div class="card mb-3"><div class="card-body">
            {!! $surrender->staff_comments !!}
		</div></div>
@endif

<h2>Character Details</h2>
<div class="card mb-3">
    <div class="card-body">
        <div class="row">
            <div class="col-md-2 mb-md-0 mb-2 mr-3">
            <img src="{{ $surrender->character->image->thumbnailurl }}"> 
            </div>
            <div class="col-md-2 mb-md-0 mb-2">
                <a href="{{ $surrender->character->url }}"><h3 class="text-uppercase">{!! $surrender->character->displayname !!}</h3></a>
                    <strong>Species</strong> - {!! $surrender->character->image->species->displayname !!}<br>
                    <strong>Subtype</strong> - {!! $surrender->character->image->subtype_id ? $surrender->character->image->subtype->displayName : 'None' !!}<br>
                    <strong>Rarity</strong> - {!! $surrender->character->image->rarity->displayname !!}<br>
                    <strong>Traits</strong> -
                    <?php $features = $surrender->character->image->features()->with('feature.category')->get(); ?>
                @if($features->count())
                        @foreach($features as $feature)
                            <div>@if($feature->feature->feature_category_id) <strong>{!! $feature->feature->category->displayName !!}:</strong> @endif {!! $feature->feature->displayName !!} @if($feature->data) ({{ $feature->data }}) @endif</div> 
                        @endforeach
                @else 
                        <div>No traits listed.</div>
                @endif
            </div>
            <div class="col-md-6 mb-md-0 mb-2">
                <h5>User Suggested worth:</h5>
                {{ $surrender->worth }}
                <br>
                <br>
                <h5>Estimated worth:</h5>
                <div class="alert alert-warning">The estimated worth will always be the amount granted to the user. If you believe more / less is the worth, edit the grant amount area.</div>
                @if($estimate == NULL)
                Calculate by traits is off
                @else
                {{ $estimate }}
                @endif
            </div>
        </div>
    </div>
</div>