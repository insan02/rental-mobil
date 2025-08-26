@extends('layout.template')
@section('title', 'Kelola Transaksi - Admin')
    
@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg border-0">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-cogs me-2"></i>Kelola Transaksi
                            </h3>
                        </div>
                    </div>
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

                    <!-- Statistics Cards -->
                    <div class="row mb-4 row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                        <div class="col">
                            <div class="card bg-warning text-white h-100">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="fw-bold">
                                            {{ \App\Models\Transaksi::where('status', 'Wait')->count() }}
                                        </h4>
                                        <p class="mb-0">Menunggu</p>
                                    </div>
                                    <i class="fas fa-clock fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="card bg-info text-white h-100">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="fw-bold">
                                            {{ \App\Models\Transaksi::where('status', 'Proses')->count() }}
                                        </h4>
                                        <p class="mb-0">Diproses</p>
                                    </div>
                                    <i class="fas fa-spinner fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col">
    <div class="card bg-danger text-white h-100">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold">
                    {{ \App\Models\Transaksi::where('status', 'Terlambat')->count() }}
                </h4>
                <p class="mb-0">Terlambat</p>
            </div>
            <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
        </div>
    </div>
</div>

<div class="col">
    <div class="card bg-secondary text-white h-100">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold">
                    {{ \App\Models\Transaksi::where('status', 'Selesai')->where('is_late_return', true)->count() }}
                </h4>
                <p class="mb-0">Selesai Terlambat</p>
            </div>
            <i class="fas fa-check-circle fa-2x opacity-75"></i>
        </div>
    </div>
</div>

                        <div class="col">
                            <div class="card bg-success text-white h-100">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="fw-bold">
                                            {{ \App\Models\Transaksi::withTrashed()->where('status', 'Selesai')->count() }}
                                        </h4>
                                        <p class="mb-0">Selesai</p>
                                    </div>
                                    <i class="fas fa-check fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Search and Filter Section -->
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <form method="GET" action="{{ route('transaksis.admin-index') }}" class="d-flex">
                                <div class="input-group">
                                    <input type="text" 
                                           name="search" 
                                           class="form-control" 
                                           placeholder="Cari berdasarkan Nama Customer, No HP, Email, Merek Mobil, atau No Polisi" 
                                           value="{{ request('search') }}"
                                           autocomplete="off">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fas fa-search"></i> Cari
                                    </button>
                                </div>
                                <!-- Hidden input untuk mempertahankan filter status -->
                                @if(request('status_filter'))
                                    <input type="hidden" name="status_filter" value="{{ request('status_filter') }}">
                                @endif
                            </form>
                        </div>
                        <div class="col-md-4">
                            <form method="GET" action="{{ route('transaksis.admin-index') }}">
                                <div class="input-group">
                                    <select name="status_filter" class="form-select" onchange="this.form.submit()">
                                        @foreach($statusOptions as $key => $label)
                                            <option value="{{ $key }}" {{ request('status_filter', 'all') === $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-outline-secondary" title="Filter">
                                        <i class="fas fa-filter"></i>
                                    </button>
                                </div>
                                <!-- Hidden input untuk mempertahankan search -->
                                @if(request('search'))
                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                @endif
                            </form>
                        </div>
                    </div>

                    <!-- Search Results Info -->
                    @if(request('search') || (request('status_filter') && request('status_filter') !== 'all'))
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
                                @if($transaksis->total() == 0)
                                    <br><small class="text-muted">Coba gunakan kata kunci yang berbeda atau reset filter</small>
                                @endif
                            </div>
                            <!-- Tombol Reset -->
                            <a href="{{ route('transaksis.admin-index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-sync-alt me-1"></i> Reset
                            </a>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th width="3%">No</th>
                                    <th>Customer</th>
                                    <th>Mobil</th>
                                    <th>Detail Sewa</th>
                                    <th>Tanggal Booking</th>
                                    <th>Tanggal Kembali Aktual</th>
                                    <th>Pengembalian Customer</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($transaksis as $key => $transaksi)
                                    <tr class="{{ $transaksi->status === 'Terlambat' ? 'table-danger' : '' }}">
                                        <td>{{ $transaksis->firstItem() + $key }}</td>
                                        
                                        <td>
                                            <div>
                                                <strong class="text-primary">{{ $transaksi->nama }}</strong><br>
                                                <small class="text-muted">
                                                    <i class="fas fa-phone me-1"></i>{{ $transaksi->ponsel }}<br>
                                                    <i class="fas fa-map-marker-alt me-1"></i>{{ Str::limit($transaksi->alamat, 30) }}
                                                </small>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <div class="d-flex align-items-center">
    @php
        $archivedFotoPath = \App\Http\Controllers\MobilController::getArchivedFotoPath($transaksi->mobil->foto ?? '');
    @endphp

    @if($archivedFotoPath)
        <img src="{{ asset('storage/' . $archivedFotoPath) }}" 
             alt="{{ $transaksi->mobil->merek ?? 'Mobil tidak tersedia' }}" 
             class="me-3 rounded shadow-sm" 
             style="width: 130px; height: 65px; object-fit: cover;"
             onerror="this.src='{{ asset('images/no-car.png') }}'; this.style.width='60px'; this.style.height='40px';">
    @else
        <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
             style="width: 60px; height: 40px;">
            <i class="fas fa-car text-muted"></i>
        </div>
    @endif

    <div>
        <strong>{{ $transaksi->mobil->merek ?? '-' }}</strong><br>
        <small class="text-muted">{{ $transaksi->mobil->nopolisi ?? '-' }}</small>
    </div>
</div>

                                        </td>
                                        
                                        <td>
                                            <small>
                                                <strong>Mulai:</strong> {{ \Carbon\Carbon::parse($transaksi->tgl_pesan)->format('d M Y') }}<br>
                                                <strong>Lama:</strong> <span class="badge bg-info">{{ $transaksi->lama }} hari</span>
                                            </small>
                                        </td>
                                        
                                        <td>{{ $transaksi->created_at->format('d M Y, H:i') }}</td>

                                        <td>
                                            {{ $transaksi->tgl_kembali->format('d M Y') }}
                                            @if($transaksi->isLate() && $transaksi->status !== 'Selesai')
                                                <br><small class="text-danger">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>Terlambat
                                                </small>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            @if($transaksi->tgl_kembali_aktual)
                                                <strong class="text-success">
                                                    {{ $transaksi->tgl_kembali_aktual->format('d M Y') }}
                                                </strong>
                                                <br><small class="text-muted">
                                                    {{ $transaksi->tgl_kembali_aktual->format('H:i') }}
                                                </small>
                                                
                                                @if($transaksi->tgl_kembali_aktual->gt($transaksi->tgl_kembali))
                                                    <br><small class="text-danger">
                                                        <i class="fas fa-clock me-1"></i>
                                                        {{ $transaksi->tgl_kembali->diffInDays($transaksi->tgl_kembali_aktual) }} hari terlambat
                                                    </small>
                                                @elseif($transaksi->tgl_kembali_aktual->lt($transaksi->tgl_kembali))
                                                    <br><small class="text-success">
                                                        <i class="fas fa-check me-1"></i>
                                                        {{ $transaksi->tgl_kembali_aktual->diffInDays($transaksi->tgl_kembali) }} hari lebih awal
                                                    </small>
                                                @else
                                                    <br><small class="text-info">
                                                        <i class="fas fa-clock me-1"></i>Tepat waktu
                                                    </small>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            <strong class="text-success fs-6">
                                                Rp {{ number_format($transaksi->total, 0, ',', '.') }}
                                            </strong>
                                        </td>
                                        
                                        <td>
    <div class="d-flex flex-column align-items-start gap-1">
        {{-- Status Dropdown --}}
        <select class="form-select form-select-sm status-select" 
                data-id="{{ $transaksi->id }}"
                style="width: auto; min-width: 120px;">
            @foreach($transaksi->getAvailableStatusOptions() as $statusKey => $statusLabel)
                <option value="{{ $statusKey }}" 
                        {{ $transaksi->status === $statusKey ? 'selected' : '' }}>
                    {{ $statusLabel }}
                </option>
            @endforeach
        </select>
        
        {{-- Status Badge dengan indikator terlambat --}}
        @if($transaksi->status === 'Selesai' && $transaksi->is_late_return)
            <span class="badge bg-warning text-dark">
                <i class="fas fa-exclamation-triangle me-1"></i>Terlambat
            </span>
        @elseif($transaksi->status !== 'Selesai' && $transaksi->isLate())
            <span class="badge bg-danger">
                <i class="fas fa-clock me-1"></i>Terlambat
            </span>
        @endif
    </div>
</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="d-flex flex-column align-items-center">
                                                @if(request('search') || (request('status_filter') && request('status_filter') !== 'all'))
                                                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                                    <h5 class="text-muted">Tidak ada hasil pencarian</h5>
                                                    <p class="text-muted mb-3">
                                                        Tidak ditemukan transaksi yang sesuai dengan kriteria pencarian
                                                    </p>
                                                    <a href="{{ route('transaksis.admin-index') }}" class="btn btn-secondary">
                                                        <i class="fas fa-arrow-left"></i> Kembali ke Semua Data
                                                    </a>
                                                @else
                                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                    <h5 class="text-muted">Belum ada transaksi aktif</h5>
                                                    <p class="text-muted mb-3">Semua transaksi sudah selesai atau belum ada transaksi yang perlu diproses.</p>
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

<script src="{{asset('assets/js/transaksi.js')}}"></script>

@endsection