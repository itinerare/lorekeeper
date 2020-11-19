<ul>
    <li class="sidebar-header"><a href="{{ url('adoptions') }}" class="card-link">{{ $name }}</a></li>
    @auth
        <li class="sidebar-section">
            <div class="sidebar-section-header">Adopt</div>
            <div class="sidebar-item"><a href="{{ url('adoptions') }}"  class="{{ set_active('adoptions') }}">{{ $name }}</a></div>
            <div class="sidebar-item"><a href="{{ url('adoptions/history') }}" class="{{ set_active('adoptions/history') }}">My Adoption History</a></div>
        </li>
        
        <li class="sidebar-section">
            <div class="sidebar-section-header">Surrender</div>
            <div class="sidebar-item"><a href="{{ url('surrenders/new') }}"  class="{{ set_active('surrenders/new') }}">New Surrender</a></div>
            <div class="sidebar-item"><a href="{{ url('surrenders') }}" >My Surrender History</a></div>
        </li>
        
    @endauth
</ul>