<div class="card mb-3 stock {{ $stock ? '' : 'hide' }}">
    <div class="card-body">
        <div class="text-right mb-3"><a href="#" class="remove-stock-button btn btn-danger">Remove</a></div>
        <div class="form-group">
            {!! Form::label('character_id['.$key.']', 'Character') !!}
            {!! Form::select('character_id['.$key.']', $characters, $stock ? $stock->character_id : null, ['class' => 'form-control stock-field', 'data-name' => 'character_id']) !!}
        </div>

        <div><a href="#" class="btn btn-primary mb-3" id="add-feature">Add Currency</a></div>
        
        <div id="featureList" class="form-group">
        </div>

        <div class="feature-row hide">
            {!! Form::label('cost', 'Cost', ['class' => 'col-form-label']) !!}
                <div class="col-4">
                    {!! Form::text('cost['.$key.']',  null, ['class' => 'form-control', 'placeholder' => 'Enter Cost']) !!}
                </div>
                <div class="col-4">
                    {!! Form::select('currency_id['.$key.']', $currencies,  null, ['class' => 'form-control', 'placeholder' => 'Select Currency']) !!}
                </div>
                <a href="#" class="remove-feature btn btn-danger">Remove</a>
        </div>
        <!--<div class="form-group">
            {!! Form::label('cost['.$key.']', 'Cost') !!}
            <div class="row">
                <div class="col-4">
                    {!! Form::text('cost['.$key.']',  null, ['class' => 'form-control stock-field', 'data-name' => 'cost']) !!}
                </div>
                <div class="col-8">
                    {!! Form::select('currency_id['.$key.']', $currencies,null, ['class' => 'form-control stock-field', 'data-name' => 'currency_id']) !!}
                </div>
            </div>
        </div>-->
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::checkbox('use_user_bank['.$key.']', 1, $stock ? $stock->use_user_bank : 1, ['class' => 'form-check-input stock-toggle stock-field', 'data-name' => 'use_user_bank']) !!}
                    {!! Form::label('use_user_bank['.$key.']', 'Use User Bank', ['class' => 'form-check-label ml-3']) !!} {!! add_help('This will allow users to purchase the character using the currency in their accounts, provided that users can own that currency.') !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-0">
                    {!! Form::checkbox('use_character_bank['.$key.']', 1, $stock ? $stock->use_character_bank : 1, ['class' => 'form-check-input stock-toggle stock-field', 'data-name' => 'use_character_bank']) !!}
                    {!! Form::label('use_character_bank['.$key.']', 'Use Character Bank', ['class' => 'form-check-label ml-3']) !!} {!! add_help('This will allow users to purchase the character using the currency belonging to characters they own, provided that characters can own that currency.') !!}
                </div>
            </div>
        </div>
        <div>
            {!! Form::label('purchase_limit['.$key.']', 'User Purchase Limit') !!} {!! add_help('This is the maximum amount of this character a user can purchase from this adoption. Set to 0 to allow infinite purchases.') !!}
            {!! Form::text('purchase_limit['.$key.']', $stock ? $stock->purchase_limit : 0, ['class' => 'form-control stock-field', 'data-name' => 'purchase_limit']) !!}
        </div>
    </div>
</div>