@extends('home.layout')

@section('home-title') Wishlists @endsection

@section('home-content')
{!! breadcrumbs(['Wishlists' => 'wishlists']) !!}

<h1>
    Wishlists
    <div class="float-right">
        <a href="#" class="btn btn-success create-wishlist"><i class="fas fa-plus"></i> Create Wishlist</a>
    </div>
</h1>

<p>These are your item wishlists. Click on the name of any wishlist to be taken to its page, where you can view and edit it as well as the items in it. Note that wishlists <strong>do not automatically update</strong>.</p>

<div>
    {!! Form::open(['method' => 'GET', 'class' => '']) !!}
        <div class="form-inline justify-content-end">
            <div class="form-group ml-3 mb-3">
                {!! Form::text('name', Request::get('name'), ['class' => 'form-control', 'placeholder' => 'Name']) !!}
            </div>
        </div>
        <div class="form-inline justify-content-end">
            <div class="form-group ml-3 mb-3">
                {!! Form::select('sort', [
                    'alpha'          => 'Sort Alphabetically (A-Z)',
                    'alpha-reverse'  => 'Sort Alphabetically (Z-A)',
                    'newest'         => 'Newest First',
                    'oldest'         => 'Oldest First'
                ], Request::get('sort') ? : 'category', ['class' => 'form-control']) !!}
            </div>
            <div class="form-group ml-3 mb-3">
                {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
            </div>
        </div>
    {!! Form::close() !!}
</div>

{!! $wishlists->render() !!}

<div class="row ml-md-2 mb-4">
    <div class="d-flex row flex-wrap col-12 pb-1 px-0 ubt-bottom">
        <div class="col-5 col-md-4 font-weight-bold">Name</div>
    </div>
    <div class="d-flex row flex-wrap col-12 mt-1 pt-2 px-0 ubt-top">
        <div class="col-5 col-md-4"> Default</div>
        <div class="col-3 col-md text-right">
            <a class="btn btn-primary btn-sm" href="{{ url('wishlists/default') }}">Edit</a>
        </div>
    </div>
    @foreach($wishlists as $wishlist)
        <div class="d-flex row flex-wrap col-12 mt-1 pt-2 px-0 ubt-top">
            <div class="col-5 col-md-4"> {{ $wishlist->name }}</div>
            <div class="col-3 col-md text-right">
                <a class="btn btn-primary btn-sm" href="{{ url('wishlists/'.$wishlist->id) }}">Edit</a>
            </div>
        </div>
    @endforeach
</div>

{!! $wishlists->render() !!}

@endsection
@section('scripts')
<script>

$( document ).ready(function() {
    $('.create-wishlist').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('wishlists/create') }}", 'Create Wishlist');
    });
});

</script>
@endsection
