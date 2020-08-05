@if($data)
    @if($data['user_items'])
        <div class="row">
        <div class="col-sm-2">
            <strong>Items:</strong>
        </div>
            <div class="col-md">
                <div class="row">   
                    @foreach($data['user_items'] as $itemRow)
                    <div class="col-sm-4">
                        <a href="/world/items?name={{ $itemRow['asset']->item->name }}">{!! $itemRow['asset']->item->name !!}</a> x{!! $itemRow['quantity'] !!}
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
    @if($data['characters'])
        <div class="row">
        <div class="col-sm-2">
            <strong>Characters:</strong>
        </div>
            <div class="col-md">
                <div class="row">
                    @foreach($data['characters'] as $character)
                    <div class="col-sm-4">
                        <a href="{{ $character['asset']->url }}">{{ $character['asset']->fullName }}</a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
    @if($data['currencies'])
        <div class="row">
        <div class="col-sm-2">
            <strong>Currencies:</strong>
        </div>
            <div class="col-md">
            @foreach($data['currencies'] as $currency)
                <div>
                    {!! $currency['asset']->display('') !!}
                </div>
            @endforeach
            </div>
        </div>
    @endif
    @if(isset($listing->data['offering_etc']) && $listing->data['offering_etc'])
        <div class="row">
        <div class="col-sm-2">
            <strong>Other:</strong>
        </div>
            <div class="col-md">
                {!! nl2br(htmlentities($listing->data['offering_etc'])) !!}
            </div>
        </div>
    @endif
@endif