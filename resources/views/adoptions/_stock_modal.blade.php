@if(!$stock)
    <div class="text-center">Invalid character selected.</div>
@else
    <div class="text-center mb-3">
        <div class="mb-1"><a href="{{ $stock->character->url }}"><img src="{{ $stock->character->image->thumbnailUrl }}" /></a></div>
        <div><a href="{{ $stock->character->url }}"><strong>{{ $stock->character->slug }}</strong></a></div>
        <div><strong>Cost: </strong> {!! $stock->currency->display($stock->cost) !!}</div>
        @if($stock->is_limited_stock) <div>Stock: {{ $stock->quantity }}</div> @endif
        @if($stock->purchase_limit) <div class="text-danger">Max {{ $stock->purchase_limit }} per user</div> @endif
    </div>

    @if($stock->character->parsed_description)
        <div class="mb-2">
            <a data-toggle="collapse" href="#characterDescription" class="h5">Description <i class="fas fa-caret-down"></i></a>
            <div class="card collapse show mt-1" id="characterDescription">
                <div class="card-body">
                    {!! $stock->character->parsed_description !!}
                </div>
            </div>
        </div>
    @endif

    @if(Auth::check())
        <h5>Purchase</h5>
        @if($stock->is_limited_stock && $stock->quantity == 0)
            <div class="alert alert-warning mb-0">This character is out of stock.</div>
        @elseif($purchaseLimitReached)
            <div class="alert alert-warning mb-0">You have already purchased the limit of {{ $stock->purchase_limit }} of this character.</div>
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
                <div class="text-right">
                    {!! Form::submit('Purchase', ['class' => 'btn btn-primary']) !!}
                </div>
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