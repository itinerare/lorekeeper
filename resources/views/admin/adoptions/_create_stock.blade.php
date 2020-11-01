@extends('admin.layout')

@section('admin-title') Adoptions @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Adoptions' => 'admin/data/adoptions', 'Adoption Stock' => 'admin/data/stock', 'Create Stock' => 'admin/data/stock/create']) !!}

{!! Form::open(['url' => 'admin/data/stock/create/new']) !!}
<h1> Create Stock </h1>
<div class="card mb-3 stock">
    <div class="card-body">
        <div class="form-group">
            {!! Form::label('character_id', 'Character') !!}
            {!! Form::select('character_id', $characters, null, ['class' => 'form-control stock-field', 'data-name' => 'character_id']) !!}
        </div>

        <div><a href="#" class="btn btn-primary mb-3" id="add-feature">Add Currency</a></div>
        
        <div id="featureList" class="form-group">
        </div>

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
        <div class="form-group">
            {!! Form::checkbox('is_visible', 1, 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
            {!! Form::label('is_visible', 'Set Viewable', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If turned off, the item will not be visible to regular users.') !!}
        </div>
        <div class="text-right">
            {!! Form::submit('Edit', ['class' => 'btn btn-primary']) !!}
        </div>
    </div>
</div>
{!! Form::close() !!}

<div class="feature-row hide">
    {!! Form::label('cost', 'Cost', ['class' => 'col-form-label']) !!}
        <div class="col-4">
            {!! Form::text('cost[]', null, ['class' => 'form-control', 'placeholder' => 'Enter Cost']) !!}
        </div>
        <div class="col-4">
            {!! Form::select('currency_id[]', $currencies,  null, ['class' => 'form-control', 'placeholder' => 'Select Currency']) !!}
        </div>
        <a href="#" class="remove-feature btn btn-danger">Remove</a>
</div>
@endsection
@section('scripts')
@parent
<script>
$( document ).ready(function() {
$('#add-feature').on('click', function(e) {
        e.preventDefault();
        addFeatureRow();
    });
    $('.remove-feature').on('click', function(e) {
        e.preventDefault();
        removeFeatureRow($(this));
    })
    function addFeatureRow() {
        var $clone = $('.feature-row').clone();
        $('#featureList').append($clone);
        $clone.removeClass('hide feature-row');
        $clone.addClass('d-flex');
        $clone.find('.remove-feature').on('click', function(e) {
            e.preventDefault();
            removeFeatureRow($(this));
        })
        $clone.find('.feature-select').selectize();
    }
    function removeFeatureRow($trigger) {
        $trigger.parent().remove();
    }
});
</script>
@endsection
