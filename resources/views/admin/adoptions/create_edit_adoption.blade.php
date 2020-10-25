@extends('admin.layout')

@section('admin-title') Adoptions @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Adoptions' => 'admin/data/adoptions', ($adoption->id ? 'Edit' : 'Create').' Adoption' => $adoption->id ? 'admin/data/adoptions/edit/'.$adoption->id : 'admin/data/adoptions/create']) !!}

<h1>{{ $adoption->id ? 'Edit' : 'Create' }} Adoption
</h1>

{!! Form::open(['url' => $adoption->id ? 'admin/data/adoptions/edit/'.$adoption->id : 'admin/data/adoptions/create', 'files' => true]) !!}

<h3>Basic Information</h3>

<div class="form-group">
    {!! Form::label('Name') !!}
    {!! Form::text('name', $adoption->name, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('Adoption Image (Optional)') !!} {!! add_help('This image is used on the adoption index and on the adoption page as a header.') !!}
    <div>{!! Form::file('image') !!}</div>
    <div class="text-muted">Recommended size: None (Choose a standard size for all adoption images)</div>
    @if($adoption->has_image)
        <div class="form-check">
            {!! Form::checkbox('remove_image', 1, false, ['class' => 'form-check-input']) !!}
            {!! Form::label('remove_image', 'Remove current image', ['class' => 'form-check-label']) !!}
        </div>
    @endif
</div>

<div class="form-group">
    {!! Form::label('Description (Optional)') !!}
    {!! Form::textarea('description', $adoption->description, ['class' => 'form-control wysiwyg']) !!}
</div>

<div class="form-group">
    {!! Form::checkbox('is_active', 1, $adoption->id ? $adoption->is_active : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
    {!! Form::label('is_active', 'Set Active', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If turned off, the adoption will not be visible to regular users.') !!}
</div>

<div class="text-right">
    {!! Form::submit($adoption->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}

<h3>Adoptable Characters</h3>

<div class="card mb-2">
    <div class="card-body row p-3">
        <div class="col col-form-label">
            <i class="fas fa-eye mr-2"></i>
            <strong><a href="Adoptable Name">Adoptable Name</a> (<a href="Species">Species</a>)</strong>
        </div>
        <div class="col col-form-label">
            5 <a href="#">Dollars</a>, 8 <a href="#">Coins</a>
        </div>
        <div class="col col-form-label">
            <i class="fas fa-paw" data-toggle="tooltip" title="Can be purchased using Character Bank"></i>
            <i class="fas fa-user" data-toggle="tooltip" title="Can be purchased using User Bank"></i>
        </div>
        <div class="col text-right">
            <a href="#" class="btn btn-dark">Edit Adoptable (Opens Modal)</a>
        </div>
    </div>
</div>

<div class="card mb-2">
    <div class="card-body row p-3">
        <div class="col col-form-label">
            <i class="fas fa-eye mr-2"></i>
            <strong><a href="Adoptable Name">Adoptable Name</a> (<a href="Species">Species</a>)</strong>
        </div>
        <div class="col col-form-label">
            5 <a href="#">Dollars</a>, 8 <a href="#">Coins</a>
        </div>
        <div class="col col-form-label">
            <i class="fas fa-paw" data-toggle="tooltip" title="Can be purchased using Character Bank"></i>
            <i class="fas fa-user" data-toggle="tooltip" title="Can be purchased using User Bank"></i>
        </div>
        <div class="col text-right">
            <a href="#" class="btn btn-dark">Edit Adoptable (Opens Modal)</a>
        </div>
    </div>
</div>

<div class="text-right">
    <a href="#" class="add-stock-button btn btn-outline-primary">Add Adoptables (Opens Modal)</a>
</div>








<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<hr>
Old Stock way below
<hr>
@if($adoption->id)
    <h3>Adoption Stock</h3>
    {!! Form::open(['url' => 'admin/data/adoptions/stock/'.$adoption->id]) !!}
        <div class="text-right mb-3">
            <a href="#" class="add-stock-button btn btn-outline-primary">Add Stock</a>
        </div>
        <div id="adoptionStock" class="row">
            @foreach($adoption->stock as $key=>$stock)
                @include('admin.adoptions._stock', ['stock' => $stock, 'key' => $key])
            @endforeach
        </div>
        <div class="text-right">
            {!! Form::submit('Edit', ['class' => 'btn btn-primary']) !!}
        </div>
    {!! Form::close() !!}
    <div id="adoptionStockData">
        @include('admin.adoptions._stock', ['stock' => null, 'key' => 0])
    </div>
@endif

@endsection

@section('scripts')
@parent
<script>
$( document ).ready(function() {
    var $adoptionStock = $('#adoptionStock');
    var $stock = $('#adoptionStockData').find('.stock');

    $('.add-stock-button').on('click', function(e) {
        e.preventDefault();

        var clone = $stock.clone();
        $adoptionStock.append(clone);
        clone.removeClass('hide');
        attachStockListeners(clone);
        refreshStockFieldNames();
    });

    attachStockListeners($('#adoptionStock .stock'));
    function attachStockListeners(stock) {
        stock.find('.stock-toggle').bootstrapToggle();
        stock.find('.stock-limited').on('change', function(e) {
            var $this = $(this);
            if($this.is(':checked')) {
                $this.parent().parent().parent().parent().find('.stock-limited-quantity').removeClass('hide');
            }
            else {
                $this.parent().parent().parent().parent().find('.stock-limited-quantity').addClass('hide');
            }
        });
        stock.find('.remove-stock-button').on('click', function(e) {
            e.preventDefault();
            $(this).parent().parent().parent().remove();
            refreshStockFieldNames();
        });
        stock.find('.card-body [data-toggle=tooltip]').tooltip({html: true});
    }
    function refreshStockFieldNames()
    {
        $('.stock').each(function(index) {
            var $this = $(this);
            var key = index;
            $this.find('.stock-field').each(function() {
                $(this).attr('name', $(this).data('name') + '[' + key + ']');
            });
        });
    }
});
    
</script>
@endsection