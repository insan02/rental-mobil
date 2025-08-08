<!-- Sidebar Start -->
<div class="sidebar pe-4 pb-3">
    <nav class="navbar bg-light navbar-light">
        <a href="{{route('home')}}" class="navbar-brand mx-4 mb-3">
            <h3 class="text-dark">
                <img src="{{ asset('assets/img/ic.png') }}" alt="RentCar Logo" width="40" height="40" class="me-2">
                RentCar
            </h3>

        </a>
        <div class="navbar-nav w-100">
            @if(data_get(auth()->user(), 'role') == 'admin')
                <a href="{{route('home')}}" class="nav-item nav-link {{Request::routeIs('home') ? 'active' : ''}}">
                    <i class="fa fa-tachometer-alt me-2"></i>Dashboard
                </a>
                <a href="{{route('mobils.index')}}" class="nav-item nav-link {{Request::routeIs('mobils.*') ? 'active' : ''}}">
                    <i class="fa fa-car me-2"></i>Mobil
                </a>
                <a href="{{route('users.index')}}" class="nav-item nav-link {{Request::routeIs('users.*') ? 'active' : ''}}">
                    <i class="fa fa-users me-2"></i>Customers
                </a>
                <a href="{{route('transaksis.admin-index')}}" class="nav-item nav-link {{Request::routeIs('transaksis.admin-index*') ? 'active' : ''}}">
                    <i class="fa fa-shopping-cart me-2"></i>Transaksi
                </a>
                <a href="{{route('transaksis.history')}}" class="nav-item nav-link {{Request::routeIs('transaksis.history*') ? 'active' : ''}}">
                    <i class="fa fa-history me-2"></i>Riwayat Transaksi
                </a>
            @endif

            @if(data_get(auth()->user(), 'role') == 'customer')
                <a href="{{route('transaksis.index')}}" class="nav-item nav-link {{Request::routeIs('transaksis.index*') ? 'active' : ''}}">
                    <i class="fa fa-shopping-cart me-2"></i>Sewa Mobil
                </a>
                <a href="{{route('transaksis.history')}}" class="nav-item nav-link {{Request::routeIs('transaksis.history*') ? 'active' : ''}}">
                    <i class="fa fa-history me-2"></i>Riwayat Transaksi
                </a>
            @endif
        </div>
    </nav>
</div>
<!-- Sidebar End -->