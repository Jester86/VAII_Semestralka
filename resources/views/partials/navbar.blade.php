<nav class="navbar navbar-expand-lg navbar-light custom-navbar mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ url('/') }}">
            Forum
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto">

                @auth
                    {{-- Welcome message --}}
                    <li class="nav-item d-flex align-items-center me-3">
                        <span class="welcome-text">
                            Welcome, {{ auth()->user()->name }}
                        </span>
                    </li>

                    {{-- Admin panel link (only for admin role) --}}
                    @if(auth()->user()->role === 'admin')
                        <li class="nav-item">
                            <a class="nav-link fw-bold admin-link" href="{{ route('admin.dashboard') }}">
                                Admin Panel
                            </a>
                        </li>
                    @endif

                    {{-- Logout link --}}
                    <li class="nav-item">
                        <a class="nav-link logout-link" href="#"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
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
