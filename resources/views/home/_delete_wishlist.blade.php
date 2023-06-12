@if($wishlist)
    {!! Form::open(['url' => 'wishlists/delete/'.$wishlist->id]) !!}

    <p>This will delete the wishlist <strong>{{ $wishlist->name }}</strong> as well as any items in it. This is not reversible. Are you sure you want to delete this wishlist?</p>

    <div class="form-group text-right">
        {!! Form::submit('Delete Wishlist', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else
    <p>Invalid wishlist selected.</p>
@endif
