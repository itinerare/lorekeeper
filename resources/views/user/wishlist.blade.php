@extends('user.layout')

@section('profile-title') {{ $user->name }}'s Wishlists: {{ $wishlist ? $wishlist->name : 'Default' }} @endsection

@section('profile-content')
{!! breadcrumbs(['Users' => 'users', $user->name => $user->url, 'Wishlists' => $user->url . '/wishlists', ($wishlist ? $wishlist->name : 'Default') => 'wishlists/'.($wishlist ? $wishlist->id : 'default')]) !!}

<h1 class="mb-4">
    Wishlist: {{ $wishlist ? $wishlist->name : 'Default' }}
</h1>

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

@if(!count($items))
    <p>No items found.</p>
@else
    {!! $items->render() !!}

    <div class="row ml-md-2 mb-4">
        <div class="d-flex row flex-wrap col-12 pb-1 px-0 ubt-bottom">
            <div class="col-5 col-md-6 font-weight-bold">Name</div>
            <div class="col-5 col-md-3 font-weight-bold">Category</div>
            <div class="col-5 col-md font-weight-bold">Count</div>
        </div>
        @foreach($items as $item)
            <div class="d-flex row flex-wrap col-12 mt-1 pt-2 px-0 ubt-top">
                <div class="col-5 col-md-6"> @if(isset($item->item->image_url)) <img class="small-icon" src="{{ $item->item->image_url }}" alt="{{ $item->item->name }}"> @endif{!! $item->item->displayName !!} </div>
                <div class="col-4 col-md-3"> {{ $item->item->category ? $item->item->category->name : '' }} </div>
                <div class="col-3 col-md">{{ $item->count }}</div>
                @if(Auth::check())
                    <div class="col-1 col-md text-right">
                        @include('widgets._wishlist_add', ['item' => $item->item])
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    {!! $items->render() !!}
@endif

@endsection
