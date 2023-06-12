<div class="{{ isset($small) && $small ? 'badge badge-success' : 'btn btn-success btn-sm' }}" id="wishlist-{{ $item->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="fas fa-plus dropdown-toggle" data-toggle="tooltip" title="Add to Wishlist"></i>
    <div class="dropdown-menu" aria-labelledby="wishlist-{{ $item->id }}">
        {!! Form::open(['url' => 'wishlists/add/'.$item->id, 'id' => 'wishlistForm-0-'.$item->id]) !!}
            <!-- check for model automatically  -->
            @php 
                $className = get_class($item);
                $baseClass = class_basename($className);
            @endphp
            <input type="hidden" name="item_type" value="{{$baseClass}}" />
            <a class="dropdown-item" href="#" onclick="document.getElementById('wishlistForm-0-{{ $item->id }}').submit();">
                Default
                @if((new App\Models\User\Wishlist)->itemCount($item->id, Auth::user(), $baseClass))
                        - {{ (new App\Models\User\Wishlist)->itemCount($item->id, Auth::user(), $baseClass) }} In Wishlist
                @endif
            </a>
        {!! Form::close() !!}
        @foreach(Auth::user()->wishlists as $wishlist)
            {!! Form::open(['url' => 'wishlists/'.$wishlist->id.'/add/'.$item->id, 'id' => 'wishlistForm-'.$wishlist->id.'-'.$item->id]) !!}
            <!-- check for model automatically  -->
            @php 
                $className = get_class($item);
                $baseClass = class_basename($className);
            @endphp
            <input type="hidden" name="item_type" value="{{$baseClass}}" />
                <a class="dropdown-item" href="#" onclick="document.getElementById('wishlistForm-{{ $wishlist->id }}-{{ $item->id }}').submit();">
                    {{ $wishlist->name }}
                    @if($wishlist->itemCount($item->id, Auth::user(), $baseClass))
                         - {{ $wishlist->itemCount($item->id, Auth::user(), $baseClass) }} In Wishlist
                    @endif
                </a>
            {!! Form::close() !!}
        @endforeach
    </div>
</div>
