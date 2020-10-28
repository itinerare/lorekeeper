@if(!$stock)
    <div class="text-center">Invalid character selected.</div>
@else
    <div class="text-center mb-3">
        <div class="mb-1"><a href="{{ $stock->character->url }}"><img src="{{ $stock->character->image->thumbnailUrl }}" /></a></div>
        <div><a href="{{ $stock->character->url }}"><strong>{{ $stock->character->slug }}</strong></a></div>
        @foreach($stock->currency as $currency)
        <div><strong>Cost: </strong>{!! $currency->cost !!} {!! $currency->currency->name !!}</div>
        @endforeach
        @if($stock->is_limited_stock) <div>Stock: {{ $stock->quantity }}</div> @endif
    </div>

    @if(Auth::check())
        <h5>Purchase</h5>
        @if($stock->is_limited_stock && $stock->quantity == 0)
            <div class="alert alert-warning mb-0">This character is out of stock.</div>
        @else 
            {!! Form::open(['url' => 'adoptions/buy']) !!}
                {!! Form::hidden('adoption_id', $adoption->id) !!}
                {!! Form::hidden('stock_id', $stock->id) !!}
                @if($stock->use_user_bank && $stock->use_character_bank)
                    <p>This character can be paid for with either your user account bank, or a character's bank. Please choose which you would like to use.</p>
                    <div class="form-group">
                        <div>
                            <label class="h5">{{ Form::radio('bank', 'user' , true, ['class' => 'bank-select mr-1']) }} User Bank</label>
                        </div>
                        <div>
                            <label class="h5">{{ Form::radio('bank', 'character' , false, ['class' => 'bank-select mr-1']) }} Character Bank</label>
                            <div class="card use-character-bank hide">
                                <div class="card-body">
                                    <p>Enter the code of the character you would like to use to purchase the character.</p>
                                    <div class="form-group">
                                        {!! Form::label('slug', 'Character Code') !!}
                                        {!! Form::text('slug', null, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif($stock->use_user_bank)
                    <p>This character will be paid for using your user account bank.</p>
                    {!! Form::hidden('bank', 'user') !!}
                @elseif($stock->use_character_bank)
                    <p>This character must be paid for using a character's bank. Enter the code of the character whose bank you would like to use to purchase the character.</p>
                    {!! Form::hidden('bank', 'character') !!}
                    <div class="form-group">
                        {!! Form::label('slug', 'Character Code') !!}
                        {!! Form::text('slug', null, ['class' => 'form-control']) !!}
                    </div>
                @endif
                @foreach($stock->currency as $currency)
                <div class="text-center">
                    {!! Form::hidden('currency_id', $currency->currency_id) !!}
                    <div class="text-uppercase text-muted">Purchase with {!! $currency->currency->name !!}</div>
                    {!! Form::submit('Purchase', ['class' => 'btn btn-primary']) !!}
                </div>
                @endforeach
            {!! Form::close() !!}
        @endif
    @else 
        <div class="alert alert-danger">You must be logged in to purchase this character.</div>
    @endif
@endif

@if(Auth::check())
    <script>
        var $useCharacterBank = $('.use-character-bank');
        $('.bank-select').on('click', function(e) {
            if($('input[name=bank]:checked').val() == 'character')
                $useCharacterBank.removeClass('hide');
            else 
                $useCharacterBank.addClass('hide');
        });

    </script>
@endif