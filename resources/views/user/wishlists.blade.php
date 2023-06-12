@extends('user.layout')

@section('profile-title') {{ $user->name }}'s Wishlists @endsection

@section('profile-content')
{!! breadcrumbs(['Users' => 'users', $user->name => $user->url, 'Wishlists' => $user->url . '/wishlists']) !!}

<h1>Wishlists</h1>

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
            <a class="btn btn-primary btn-sm" href="{{ url('user/'.$user->name.'/wishlists/default') }}">View</a>
        </div>
    </div>
    @foreach($wishlists as $wishlist)
        <div class="d-flex row flex-wrap col-12 mt-1 pt-2 px-0 ubt-top">
            <div class="col-5 col-md-4"> {{ $wishlist->name }}</div>
            <div class="col-3 col-md text-right">
                <a class="btn btn-primary btn-sm" href="{{ url('user/'.$user->name.'/wishlists/'.$wishlist->id) }}">View</a>
            </div>
        </div>
    @endforeach
</div>

{!! $wishlists->render() !!}

@endsection
