<ul>
    <li class="sidebar-header"><a href="{{ url('adoptions') }}" class="card-link">Adoptions</a></li>
    
    @if(Auth::check())
        <li class="sidebar-section">
            <div class="sidebar-section-header">History</div>
            <div class="sidebar-item"><a href="{{ url('adoptions/history') }}" class="{{ set_active('adoptions/history') }}">My Purchase History</a></div>
        </li>
    @endif

    <li class="sidebar-section">
        <div class="sidebar-section-header">Adoptions</div>
        @foreach($adoptions as $adoption)
            <div class="sidebar-item"><a href="{{ $adoption->url }}" class="{{ set_active('adoptions/'.$adoption->id) }}">{{ $adoption->name }}</a></div>
        @endforeach
    </li>
</ul>