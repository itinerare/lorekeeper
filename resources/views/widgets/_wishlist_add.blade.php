<div class="{{ isset($small) && $small ? 'badge badge-success' : 'btn btn-success btn-sm' }}" id="wishlist-{{ $item->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="fas fa-plus dropdown-toggle" data-toggle="tooltip" title="Add to Wishlist"></i>
    <div class="dropdown-menu" aria-labelledby="wishlist-{{ $item->id }}">
        {!! Form::open(['url' => 'wishlists/add/'.$item->id, 'id' => 'wishlistForm-0-'.$item->id]) !!}
            <a class="dropdown-item" href="#" onclick="document.getElementById('wishlistForm-0-{{ $item->id }}').submit();">
                Default
                @if((new App\Models\User\Wishlist)->itemCount($item->id, Auth::user()))
                        - {{ (new App\Models\User\Wishlist)->itemCount($item->id, Auth::user()) }} In Wishlist
                @endif
            </a>
        {!! Form::close() !!}
        @foreach(Auth::user()->wishlists as $wishlist)
            {!! Form::open(['url' => 'wishlists/'.$wishlist->id.'/add/'.$item->id, 'id' => 'wishlistForm-'.$wishlist->id.'-'.$item->id]) !!}
                <a class="dropdown-item" href="#" onclick="document.getElementById('wishlistForm-{{ $wishlist->id }}-{{ $item->id }}').submit();">
                    {{ $wishlist->name }}
                    @if($wishlist->itemCount($item->id, Auth::user()))
                         - {{ $wishlist->itemCount($item->id, Auth::user()) }} In Wishlist
                    @endif
                </a>
            {!! Form::close() !!}
        @endforeach
    </div>
</div>
