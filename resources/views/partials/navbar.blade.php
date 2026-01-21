<nav class="navbar navbar-expand-lg navbar-light custom-navbar mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ auth()->check() ? url('/dashboard') : route('categories.index') }}">
            Dashboard
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto">

                {{-- Forum link - visible to all --}}
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('categories.index') }}">
                        Forum
                    </a>
                </li>

                @auth
                    {{-- Messages link --}}
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('messages.inbox') }}">
                            Messages
                            @php $unreadCount = auth()->user()->unreadMessagesCount(); @endphp
                            @if($unreadCount > 0)
                                <span class="badge bg-danger">{{ $unreadCount }}</span>
                            @endif
                        </a>
                    </li>

                    {{-- Profile link --}}
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('profile.show', auth()->user()) }}">
                            My Profile
                        </a>
                    </li>

                    {{-- Admin panel link (only for admin role) --}}
                    @if(auth()->user()->role === 'admin')
                        <li class="nav-item">
                            <a class="nav-link fw-bold admin-link" href="{{ route('admin.adminPanel') }}">
                                Admin Panel
                            </a>
                        </li>
                    @endif

                    {{-- Logout link --}}
                    <li class="nav-item">
                        <a class="nav-link logout-link" href="#"
                           onclick="event.preventDefault(); clearSessionUptime(); document.getElementById('logout-form').submit();">
                            Logout
                        </a>
                    </li>

                    {{-- Hidden Logout Form --}}
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>

                @else
                    {{-- Guest links --}}
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">Register</a>
                    </li>
                @endauth

            </ul>
        </div>
    </div>
</nav>
