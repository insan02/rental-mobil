@extends('layout.template')
@section('title', 'Riwayat Transaksi - RentCar')
    
@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg border-0">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-history me-2"></i>
                                {{ Auth::user()->role === 'admin' ? 'Transaksi Selesai' : 'Riwayat Transaksi Saya' }}
                            </h3>
                        </div>
                    </div>

                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('transaksis.cetak', request()->query()) }}" class="btn btn-danger btn-sm">
                            <i class="fas fa-file-pdf me-1"></i> Cetak Laporan PDF
                        </a>
                    @endif

                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Search and Filter Section -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('transaksis.history') }}" class="d-flex">
                                <div class="input-group">
                                    <input type="text" 
                                           name="search" 
                                           class="form-control" 
                                           placeholder="{{ Auth::user()->role === 'admin' 
                                               ? 'Cari berdasarkan Customer, Mobil, Email, atau Total' 
                                               : 'Cari berdasarkan Merek Mobil, No Polisi, atau Total' }}" 
                                           value="{{ request('search') }}"
                                           autocomplete="off">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fas fa-search"></i> Cari
                                    </button>
                                </div>
                                <!-- Hidden inputs untuk mempertahankan filter -->
                                @if(request('status_filter'))
                                    <input type="hidden" name="status_filter" value="{{ request('status_filter') }}">
                                @endif
                                @if(request('date_filter'))
                                    <input type="hidden" name="date_filter" value="{{ request('date_filter') }}">
                                @endif
                            </form>
                        </div>
                        <div class="col-md-3">
                            <form method="GET" action="{{ route('transaksis.history') }}">
                                <select name="status_filter" class="form-select" onchange="this.form.submit()">
                                    @foreach($statusOptions as $key => $label)
                                        <option value="{{ $key }}" {{ request('status_filter', 'all') === $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <!-- Hidden inputs -->
                                @if(request('search'))
                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                @endif
                                @if(request('date_filter'))
                                    <input type="hidden" name="date_filter" value="{{ request('date_filter') }}">
                                @endif
                            </form>
                        </div>
                        <div class="col-md-3">
                            <form method="GET" action="{{ route('transaksis.history') }}">
                                <select name="date_filter" class="form-select" onchange="this.form.submit()">
                                    @foreach($dateOptions as $key => $label)
                                        <option value="{{ $key }}" {{ request('date_filter', 'all') === $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <!-- Hidden inputs -->
                                @if(request('search'))
                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                @endif
                                @if(request('status_filter'))
                                    <input type="hidden" name="status_filter" value="{{ request('status_filter') }}">
                                @endif
                            </form>
                        </div>
                    </div>

                    <!-- Search Results Info -->
                    @if(request('search') || (request('status_filter') && request('status_filter') !== 'all') || (request('date_filter') && request('date_filter') !== 'all'))
                        <div class="alert alert-info mb-3 d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-info-circle me-2"></i>
                                Menampilkan {{ $transaksis->total() }} hasil 
                                @if(request('search'))
                                    untuk pencarian "<strong>{{ request('search') }}</strong>"
                                @endif
                                @if(request('status_filter') && request('status_filter') !== 'all')
                                    dengan status "<strong>{{ $statusOptions[request('status_filter')] ?? request('status_filter') }}</strong>"
                                @endif
                                @if(request('date_filter') && request('date_filter') !== 'all')
                                    pada periode "<strong>{{ $dateOptions[request('date_filter')] ?? request('date_filter') }}</strong>"
                                @endif
                                @if($transaksis->total() == 0)
                                    <br><small class="text-muted">Coba gunakan kata kunci yang berbeda atau reset filter</small>
                                @endif
                            </div>
                            <!-- Tombol Reset -->
                            <a href="{{ route('transaksis.history') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-sync-alt me-1"></i> Reset
                            </a>
                        </div>
                    @endif

                    <!-- Statistics Cards (hanya untuk admin) -->
                    @if(Auth::user()->role === 'admin')
                        <div class="row mb-4 row-cols-1 row-cols-md-5 g-3">
                            <!-- Total Selesai -->
                            <div class="col">
                                <div class="card bg-success text-white h-100">
                                    <div class="card-body text-center">
                                        <h4 class="fw-bold">
                                            {{ \App\Models\Transaksi::withTrashed()->where('status', 'Selesai')->count() }}
                                        </h4>
                                        <p class="mb-0 small">Total Selesai</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Lebih Awal -->
                            <div class="col">
                                <div class="card bg-secondary text-white h-100">
                                    <div class="card-body text-center">
                                        <h4 class="fw-bold">
                                            {{ \App\Models\Transaksi::withTrashed()
                                                ->where('status', 'Selesai')
                                                ->whereNotNull('tgl_kembali_aktual')
                                                ->whereRaw('DATE(tgl_kembali_aktual) < DATE(tgl_kembali)')
                                                ->count() }}
                                        </h4>
                                        <p class="mb-0 small">Lebih Awal</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Tepat Waktu -->
                            <div class="col">
                                <div class="card bg-info text-white h-100">
                                    <div class="card-body text-center">
                                        <h4 class="fw-bold">
                                            {{ \App\Models\Transaksi::withTrashed()
                                                ->where('status', 'Selesai')
                                                ->whereNotNull('tgl_kembali_aktual')
                                                ->whereRaw('DATE(tgl_kembali_aktual) = DATE(tgl_kembali)')
                                                ->count() }}
                                        </h4>
                                        <p class="mb-0 small">Tepat Waktu</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Terlambat -->
                            <div class="col">
                                <div class="card bg-warning text-white h-100">
                                    <div class="card-body text-center">
                                        <h4 class="fw-bold">
                                            {{ \App\Models\Transaksi::withTrashed()
                                                ->where('status', 'Selesai')
                                                ->whereNotNull('tgl_kembali_aktual')
                                                ->whereRaw('DATE(tgl_kembali_aktual) > DATE(tgl_kembali)')
                                                ->count() }}
                                        </h4>
                                        <p class="mb-0 small">Terlambat</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Revenue -->
                            <div class="col">
                                <div class="card bg-primary text-white h-100">
                                    <div class="card-body text-center">
                                        <h4 class="fw-bold">
                                            Rp {{ number_format(\App\Models\Transaksi::withTrashed()
                                                ->where('status', 'Selesai')
                                                ->sum('total'), 0, ',', '.') }}
                                        </h4>
                                        <p class="mb-0 small">Total Pendapatan</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif


                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th width="5%">No</th>
                                    @if(Auth::user()->role === 'admin')
                                        <th>Customer</th>
                                    @endif
                                    <th>Mobil</th>
                                    <th>Tanggal Booking</th>
                                    <th>Tanggal Kembali Aktual</th>
                                    <th>Lama</th>
                                    <th>Pengembalian Customer</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($transaksis as $key => $transaksi)
                                    <tr class="{{ $transaksi->status === 'Terlambat' ? 'table-danger' : '' }}">
                                        <td>{{ $transaksis->firstItem() + $key }}</td>
                                        
                                        @if(Auth::user()->role === 'admin')
                                        <td>
                                            <div>
                                                <strong>{{ $transaksi->nama }}</strong><br>
                                                <small class="text-muted">{{ $transaksi->ponsel }}</small><br>
                                                <small class="text-muted">
                                                    <i class="fas fa-map-marker-alt me-1"></i>
                                                    {{ Str::limit($transaksi->alamat, 20) }}
                                                </small>
                                            </div>
                                        </td>
                                        @endif
                                        
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @php
    $archivedFotoPath = \App\Http\Controllers\MobilController::getArchivedFotoPath($transaksi->mobil->foto ?? '');
@endphp

@if($archivedFotoPath)
    <img src="{{ asset('storage/' . $archivedFotoPath) }}" 
         alt="{{ $transaksi->mobil->merek ?? 'Mobil tidak tersedia' }}" 
         class="me-3 rounded" 
         style="width: 130px; height: 65px; object-fit: cover;"
         onerror="this.src='{{ asset('images/no-car.png') }}'; this.style.width='60px'; this.style.height='40px';">
@else
    <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
         style="width: 60px; height: 40px;">
        <i class="fas fa-car text-muted"></i>
    </div>
@endif
                                                <div>
                                                    <strong>{{ $transaksi->mobil->merek }}</strong><br>
                                                    <small class="text-muted">{{ $transaksi->mobil->nopolisi }}</small><br>
                                                    
                                                    <small class="text-muted">
                                                        <i class="fas fa-tag me-1"></i>{{ $transaksi->mobil->jenis }}
                                                    </small><br>

                                                    <small class="text-muted">
                                                        <i class="fas fa-users me-1"></i>{{ $transaksi->mobil->kapasitas }}
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td>{{ $transaksi->created_at->format('d M Y') }}</td>
                                        
                                        <td>
                                            {{ $transaksi->tgl_kembali->format('d M Y') }}
                                            @if($transaksi->isLate() && $transaksi->status !== 'Selesai')
                                                <br><small class="text-danger">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>Terlambat
                                                </small>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            <span class="badge bg-info">{{ $transaksi->lama }} hari</span>
                                        </td>

                                        <td>
                                            @if($transaksi->tgl_kembali_aktual)
                                                <strong class="text-success">
                                                    {{ $transaksi->tgl_kembali_aktual->format('d M Y') }}
                                                </strong>
                                                @if($transaksi->tgl_kembali_aktual->gt($transaksi->tgl_kembali))
                                                    <br><small class="text-danger">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>Terlambat
                                                    </small>
                                                @elseif($transaksi->tgl_kembali_aktual->eq($transaksi->tgl_kembali))
                                                    <br><small class="text-success">
                                                        <i class="fas fa-check me-1"></i>Tepat waktu
                                                    </small>
                                                @else
                                                    <br><small class="text-info">
                                                        <i class="fas fa-thumbs-up me-1"></i>Lebih awal
                                                    </small>
                                                @endif
                                            @else
                                                @if($transaksi->status === 'Selesai')
                                                    <span class="text-warning">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>Data tidak tersedia
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            @endif
                                        </td>
                                        
                                        <td>
                                            <strong class="text-success">
                                                Rp {{ number_format($transaksi->total, 0, ',', '.') }}
                                            </strong>
                                        </td>
                                        
                                        <td>
                                            <span class="badge 
                                                @if($transaksi->status === 'Wait') bg-warning
                                                @elseif($transaksi->status === 'Proses') bg-info
                                                @elseif($transaksi->status === 'Selesai') bg-success
                                                @else bg-danger
                                                @endif">
                                                {{ $transaksi->status }}
                                            </span>
                                        </td>
                                        
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                @if(Auth::user()->role === 'admin')
                                                    <!-- Admin Actions -->
                                                    <form action="{{ route('transaksis.destroy', $transaksi) }}" 
                                                        method="POST" class="d-inline form-delete-transaksi">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    @if($transaksi->canCustomerEdit())
                                                        <a href="{{ route('transaksis.edit', $transaksi) }}" 
                                                        class="btn btn-warning" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @else
                                                        <button class="btn btn-secondary" disabled title="Tidak dapat diedit">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    @endif
                                                    
                                                    @if($transaksi->canCustomerDelete())
                                                        <form action="{{ route('transaksis.destroy', $transaksi) }}" 
                                                            method="POST" class="d-inline form-delete-transaksi">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger" title="Hapus">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <button class="btn btn-secondary" disabled title="Tidak dapat dihapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ Auth::user()->role === 'admin' ? '10' : '9' }}" class="text-center py-4">
                                            <div class="d-flex flex-column align-items-center">
                                                @if(request('search') || (request('status_filter') && request('status_filter') !== 'all') || (request('date_filter') && request('date_filter') !== 'all'))
                                                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                                    <h5 class="text-muted">Tidak ada hasil pencarian</h5>
                                                    <p class="text-muted mb-3">
                                                        Tidak ditemukan transaksi yang sesuai dengan kriteria pencarian
                                                    </p>
                                                    <a href="{{ route('transaksis.history') }}" class="btn btn-secondary">
                                                        <i class="fas fa-arrow-left"></i> Kembali ke Semua Data
                                                    </a>
                                                @else
                                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                    <h5 class="text-muted">Belum ada transaksi</h5>
                                                    @if(Auth::user()->role !== 'admin')
                                                        <p class="text-muted mb-3">Silakan lakukan booking mobil terlebih dahulu.</p>
                                                        <a href="{{ route('transaksis.index') }}" class="btn btn-primary">
                                                            <i class="fas fa-plus me-2"></i>Booking Sekarang
                                                        </a>
                                                    @else
                                                        <p class="text-muted">Belum ada transaksi yang selesai.</p>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Improved Pagination Section -->
                    @if($transaksis->hasPages())
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <span class="text-muted">
                                        Total Keseluruhan: {{ $transaksis->total() }} data
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-end">
                                    <nav aria-label="Pagination Navigation">
                                        <ul class="pagination pagination-sm mb-0">
                                            {{-- Previous Page Link --}}
                                            @if ($transaksis->onFirstPage())
                                                <li class="page-item disabled">
                                                    <span class="page-link">
                                                        <i class="fas fa-chevron-left"></i>
                                                    </span>
                                                </li>
                                            @else
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $transaksis->previousPageUrl() }}" rel="prev">
                                                        <i class="fas fa-chevron-left"></i>
                                                    </a>
                                                </li>
                                            @endif

                                            {{-- Pagination Elements --}}
                                            @foreach ($transaksis->getUrlRange(1, $transaksis->lastPage()) as $page => $url)
                                                @if ($page == $transaksis->currentPage())
                                                    <li class="page-item active">
                                                        <span class="page-link">{{ $page }}</span>
                                                    </li>
                                                @else
                                                    <li class="page-item">
                                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                                    </li>
                                                @endif
                                            @endforeach

                                            {{-- Next Page Link --}}
                                            @if ($transaksis->hasMorePages())
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $transaksis->nextPageUrl() }}" rel="next">
                                                        <i class="fas fa-chevron-right"></i>
                                                    </a>
                                                </li>
                                            @else
                                                <li class="page-item disabled">
                                                    <span class="page-link">
                                                        <i class="fas fa-chevron-right"></i>
                                                    </span>
                                                </li>
                                            @endif
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('assets/js/history.js') }}"></script>

@endsection