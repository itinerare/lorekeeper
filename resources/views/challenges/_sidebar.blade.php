<ul>
    <li class="sidebar-header"><a href="{{ url('challenges') }}" class="card-link">Challenges</a></li>
    @if(Auth::check())
        <li class="sidebar-section">
            <div class="sidebar-section-header">Challenges</div>
            <div class="sidebar-item"><a href="{{ url('challenges/my-challenges') }}" class="{{ set_active('challenges/my-challenges*') }}">My Challenges</a></div>
        </li>
    @endif
</ul>
