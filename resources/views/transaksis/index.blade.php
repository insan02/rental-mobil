@extends('layout.template')
@section('title', 'Pilih Mobil - RentCar')
    
@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-dark">
                    <i class="fas fa-car me-2"></i>Pilih Mobil untuk Disewa
                </h2>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <!-- Search and Filter -->
            <div class="row mb-4 align-items-end">
                <!-- Kolom Pencarian -->
                <div class="col-md-6 mb-2">
                    <form method="GET" action="{{ route('transaksis.index') }}">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Cari merek, no polisi, kapasitas..." value="{{ request('search') }}" autocomplete="off">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-search"></i> Cari
                            </button>
                        </div>
                        @if(request('jenis_filter'))
                            <input type="hidden" name="jenis_filter" value="{{ request('jenis_filter') }}">
                        @endif
                    </form>
                </div>

                <!-- Kolom Filter + Reset dalam satu baris -->
                <div class="col-md-6 mb-2">
                    <div class="d-flex">
                        <form method="GET" action="{{ route('transaksis.index') }}" class="flex-grow-1 me-2">
                            <div class="input-group">
                                <select name="jenis_filter" class="form-select" onchange="this.form.submit()">
                                    @foreach($jenisOptions as $key => $label)
                                        <option value="{{ $key }}" {{ request('jenis_filter', 'all') === $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @if(request('search'))
                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                @endif
                            </div>
                        </form>

                        @if(request('search') || (request('jenis_filter') && request('jenis_filter') !== 'all'))
                            <a href="{{ route('transaksis.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-sync-alt"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>


            <div class="row">
                @forelse($mobils as $mobil)
                    <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-img-top position-relative d-flex align-items-center justify-content-center" style="height: 200px; background-color: #f8f9fa;">
                                @if($mobil->foto)
                                    <img src="{{ asset('storage/' . $mobil->foto) }}" 
                                         alt="{{ $mobil->merek }}" 
                                         class="img-fluid"
                                         style="max-width: 100%; max-height: 100%; object-fit: contain; object-position: center;">
                                @else
                                    <div class="d-flex align-items-center justify-content-center h-100">
                                        <i class="fas fa-car text-muted" style="font-size: 4rem;"></i>
                                    </div>
                                @endif
                                
                                <!-- Badge for Car Type -->
                                <div class="position-absolute top-0 start-0 m-2">
                                    <span class="badge 
                                        @if($mobil->jenis === 'Sedan') bg-info
                                        @elseif($mobil->jenis === 'MPV') bg-warning
                                        @else bg-success
                                        @endif fs-6">
                                        {{ $mobil->jenis }}
                                    </span>
                                </div>
                            </div>

                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title text-primary mb-2">
                                    <i class="fas fa-car me-2"></i>{{ $mobil->merek }}
                                </h5>
                                
                                <div class="mb-3">
                                    <div class="row g-2">
                                        <div class="col-12">
                                            <small class="text-muted d-block">
                                                <i class="fas fa-id-card me-1"></i>No. Polisi: 
                                                <strong>{{ $mobil->nopolisi }}</strong>
                                            </small>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">
                                                <i class="fas fa-users me-1"></i>Kapasitas: 
                                                <strong>{{ $mobil->kapasitas }}</strong>
                                            </small>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">
                                                <i class="fas fa-cog me-1"></i>Jenis: 
                                                <strong>{{ $mobil->jenis }}</strong>
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <span class="text-muted small">Harga per hari</span>
                                            <h4 class="text-success mb-0 fw-bold">
                                                Rp {{ number_format($mobil->harga, 0, ',', '.') }}
                                            </h4>
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid">
                                        <a href="{{ route('transaksis.create', ['mobil_id' => $mobil->id]) }}" 
                                            class="btn btn-primary btn-lg">
                                                <i class="fas fa-calendar-check me-2"></i>Sewa Sekarang
                                            </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="fas fa-car text-muted mb-3" style="font-size: 4rem;"></i>
                            <h4 class="text-muted">Tidak ada mobil tersedia</h4>
                            <p class="text-muted">Mohon coba lagi nanti atau hubungi admin.</p>
                        </div>
                    </div>
                @endforelse

                <!-- Pagination -->
                @if($mobils->hasPages())
                    <div class="mt-4 d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Menampilkan {{ $mobils->firstItem() }} - {{ $mobils->lastItem() }} dari total {{ $mobils->total() }} mobil
                        </div>
                        <div>
                            {{ $mobils->withQueryString()->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>

@endsection