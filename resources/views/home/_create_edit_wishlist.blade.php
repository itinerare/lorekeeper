@if($wishlist)
    {!! Form::open(['url' => $wishlist->id ? 'wishlists/edit/'.$wishlist->id : 'wishlists/create']) !!}

    <div class="form-group">
        {!! Form::label('name', 'Name') !!}
        {!! add_help('Enter a name for the wishlist.') !!}
        {!! Form::text('name', $wishlist->name, ['class' => 'form-control', 'required']) !!}
    </div>

    <div class="form-group text-right">
        {!! Form::submit('Submit', ['class' => 'btn btn-success']) !!}
    </div>

    {!! Form::close() !!}
@else
    <p>Invalid wishlist selected.</p>
@endif
