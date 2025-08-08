<!-- Navbar Start -->
<nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-0">
    <a href="{{route('home')}}" class="navbar-brand d-flex d-lg-none me-4">
        <h2 class="text-primary mb-0"><i class="fa fa-hashtag"></i></h2>
    </a>
    <button type="button" class="sidebar-toggler flex-shrink-0">
        <i class="fa fa-bars"></i>
    </button>
    
    <div class="navbar-nav align-items-center ms-auto">
        <div class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
                @if(auth()->user()->foto && Storage::disk('public')->exists(auth()->user()->foto))
                    <img class="rounded-circle" src="{{ Storage::url(auth()->user()->foto) }}" alt="User Photo" style="width: 40px; height: 40px; object-fit: cover;">
                @else
                    <img class="rounded-circle" src="{{ asset('assets/img/user.png') }}" alt="Default Photo" style="width: 40px; height: 40px; object-fit: cover;">
                @endif

                <div class="d-none d-lg-flex flex-column align-items-start ms-2">
                    <span class="fw-semibold">{{ auth()->user()->name }}</span>
                    @if(auth()->user()->role === 'admin')
                        <small class="text-muted text-capitalize">{{ auth()->user()->role }}</small>
                    @endif
                </div>
            </a>

            <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                <a href="{{route('profile.show')}}" class="dropdown-item">
                    <i class="fas fa-user me-2"></i>Profil
                </a>
                <a href="#" onclick="confirmLogout(event)" data-url="{{route('login.keluar')}}" class="dropdown-item text-danger">
                    <i class="fas fa-sign-out-alt me-2"></i>Log Out
                </a>
            </div>
        </div>
    </div>
</nav>
<!-- Navbar End -->
