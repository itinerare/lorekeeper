<div class="card mb-3 stock">
    <div class="card-body">
        <div class="text-right mb-3"><a href="#" class="remove-stock-button btn btn-danger">Remove</a></div>
        <div class="form-group">
            {!! Form::label('character_id', 'Character') !!}
            {!! Form::select('character_id', $characters, null, ['class' => 'form-control stock-field', 'data-name' => 'character_id']) !!}
        </div>

        <div class="form-group currency-select">
            {!! Form::label('cost', 'Cost') !!}
            <div class="row">
                <div class="col-4">
                    {!! Form::text('cost', null, ['class' => 'form-control stock-field', 'data-name' => 'cost']) !!}
                </div>
                <div class="d-flex currencyList mb-2 col-4">
                    {!! Form::select('currency_id', $currencies,  null, ['class' => 'form-control stock-field', 'data-name' => 'currency_id']) !!}
                    <a href="#" class="remove-currency btn ml-2 btn-danger mb-2">×</a>
                </div>
            </div>
            <div class="col-12 text-right"><a href="#" class="btn btn-primary" id="add-currency">Add Currency</a></div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::checkbox('use_user_bank', 1, 1, ['class' => 'form-check-input stock-toggle stock-field', 'data-name' => 'use_user_bank']) !!}
                    {!! Form::label('use_user_bank', 'Use User Bank', ['class' => 'form-check-label ml-3']) !!} {!! add_help('This will allow users to purchase the character using the currency in their accounts, provided that users can own that currency.') !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-0">
                    {!! Form::checkbox('use_character_bank', 1, 1, ['class' => 'form-check-input stock-toggle stock-field', 'data-name' => 'use_character_bank']) !!}
                    {!! Form::label('use_character_bank', 'Use Character Bank', ['class' => 'form-check-label ml-3']) !!} {!! add_help('This will allow users to purchase the character using the currency belonging to characters they own, provided that characters can own that currency.') !!}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="currency-row hide mb-2 col-4">
    {!! Form::label('cost', 'Cost') !!}
    <div class="row">
        <div class="col-4">
            {!! Form::text('cost', null, ['class' => 'form-control stock-field', 'data-name' => 'cost']) !!}
        </div>
        <div class="d-flex mb-2 col-4">
            {!! Form::select('currency_id', $currencies, null, ['class' => 'form-control stock-field', 'data-name' => 'currency_id']) !!}
            <a href="#" class="remove-currency btn ml-2 btn-danger mb-2">×</a>
        </div>
    </div>
</div>

@section('scripts')
@parent
<script>
$( document ).ready(function() {
    $('.selectize').selectize();

    $('.original.currency-select').selectize();
    $('#add-currency').on('click', function(e) {
        e.preventDefault();
        addCurrencyRow();
    });
    $('.remove-currency').on('click', function(e) {
        e.preventDefault();
        removeCurrencyRow($(this));
    })
    function addCurrencyRow() {
        var $clone = $('.currency-row').clone();
        $('#currencyList').append($clone);
        $clone.removeClass('hide currency-row');
        $clone.addClass('d-flex');
        $clone.find('.remove-currency').on('click', function(e) {
            e.preventDefault();
            removeCurrencyRow($(this));
        })
        $clone.find('.currency-select').selectize();
    }
    function removeCurrencyRow($trigger) {
        $trigger.parent().remove();
    }
});
    
</script>
@endsection
