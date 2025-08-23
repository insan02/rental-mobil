<!-- Navbar Start -->
<nav class="navbar navbar-expand-lg bg-light navbar-light sticky-top py-0 
     @if(auth()->user()->role === 'customer') px-4 @else px-4 @endif">
    <!-- Logo Brand -->
    <div class="navbar-brand d-flex align-items-center me-4">
        <h3 class="text-dark mb-0">
            <img src="{{ asset('assets/img/ic.png') }}" alt="RentCar Logo" width="40" height="40" class="me-2">
            RentCar
        </h3>
    </div>

    
    <!-- Toggle button for mobile -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    
    <!-- Navbar Menu -->
    <div class="collapse navbar-collapse" id="navbarNav">
        <!-- Menu untuk Customer (tampil jika user adalah customer) -->
        @if(auth()->user()->role === 'customer')
        <ul class="navbar-nav me-auto">
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('transaksis.index') ? 'active' : '' }}" href="{{route('transaksis.index')}}">
                    <i class="fas fa-car me-2"></i>Sewa Mobil
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('transaksis.history') ? 'active' : '' }}" href="{{route('transaksis.history')}}">
                    <i class="fas fa-history me-2"></i>Riwayat Transaksi
                </a>
            </li>
        </ul>
        @endif
        
        <!-- User Profile Dropdown (di sebelah kanan) -->
        <div class="navbar-nav ms-auto">
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
                    @if(auth()->user()->foto && Storage::disk('public')->exists(auth()->user()->foto))
                        <img class="rounded-circle" src="{{ Storage::url(auth()->user()->foto) }}" alt="User Photo" style="width: 40px; height: 40px; object-fit: cover;">
                    @else
                        <img class="rounded-circle" src="{{ asset('assets/img/user.png') }}" alt="Default Photo" style="width: 40px; height: 40px; object-fit: cover;">
                    @endif

                    <div class="d-none d-lg-flex flex-column align-items-start ms-2">
                        <span class="fw-semibold">{{ auth()->user()->name }}</span>
                        <small class="text-muted text-capitalize">{{ auth()->user()->role }}</small>
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
    </div>
</nav>
<!-- Navbar End -->