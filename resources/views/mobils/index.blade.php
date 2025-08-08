@extends('layout.template')

@section('title', 'Kelola Mobil - RentCar')

@section('content')

<div class="container-fluid pt-4 px-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Data Mobil</h3>
                    <a href="{{ route('mobils.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah
                    </a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Search Section -->
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <form method="GET" action="{{ route('mobils.index') }}" class="d-flex">
                                <div class="input-group">
                                    <input type="text" 
                                           name="search" 
                                           class="form-control" 
                                           placeholder="Cari berdasarkan No Polisi, Merek, Jenis, atau Kapasitas" 
                                           value="{{ request('search') }}"
                                           autocomplete="off">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fas fa-search"></i> Cari
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Search Results Info -->
                    @if(request('search') || request('jenis_filter'))
                        <div class="alert alert-info mb-3 d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-info-circle me-2"></i>
                                Menampilkan {{ $mobils->total() }} hasil 
                                @if(request('search'))
                                    untuk pencarian "<strong>{{ request('search') }}</strong>"
                                @endif
                                @if($mobils->total() == 0)
                                    <br><small class="text-muted">Coba gunakan kata kunci yang berbeda atau reset filter</small>
                                @endif
                            </div>
                            <!-- Tombol Reset -->
                            <a href="{{ route('mobils.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-sync-alt me-1"></i> Reset
                            </a>
                        </div>
                    @endif


                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Foto</th>
                                    <th>No Polisi</th>
                                    <th>Merek</th>
                                    <th>Jenis</th>
                                    <th>Kapasitas</th>
                                    <th>Harga</th>
                                    <th width="15%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($mobils as $key => $mobil)
                                    <tr>
                                        <td>{{ $mobils->firstItem() + $key }}</td>
                                        <td>
                                            @if($mobil->foto)
                                                <img src="{{ asset('storage/' . $mobil->foto) }}" 
                                                    alt="{{ $mobil->merek }}" 
                                                    class="img-fluid rounded" 
                                                    style="max-width: 150px; max-height: 150px;">
                                            @else
                                                <div class="bg-secondary text-white d-flex align-items-center justify-content-center rounded" 
                                                    style="width: 150px; height: 150px;">
                                                    <i class="fas fa-car fa-2x"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ $mobil->nopolisi }}</td>
                                        <td>{{ $mobil->merek }}</td>
                                        <td>
                                            <span class="badge 
                                                @if($mobil->jenis === 'Sedan') bg-info
                                                @elseif($mobil->jenis === 'MPV') bg-warning
                                                @else bg-success
                                                @endif">
                                                {{ $mobil->jenis }}
                                            </span>
                                        </td>
                                        <td>{{ $mobil->kapasitas }}</td>
                                        <td>Rp {{ number_format($mobil->harga, 0, ',', '.') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('mobils.edit', $mobil) }}" 
                                                   class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('mobils.destroy', $mobil) }}" 
                                                      method="POST" class="d-inline form-delete">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" 
                                                            class="btn btn-danger btn-sm btn-delete" 
                                                            data-merek="{{ $mobil->merek }}"
                                                            data-nopolisi="{{ $mobil->nopolisi }}"
                                                            title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="d-flex flex-column align-items-center">
                                                @if(request('search') || request('jenis_filter'))
                                                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                                    <h5 class="text-muted">Tidak ada hasil pencarian</h5>
                                                    <p class="text-muted mb-3">
                                                        Tidak ditemukan mobil yang sesuai dengan kriteria pencarian
                                                    </p>
                                                    <a href="{{ route('mobils.index') }}" class="btn btn-secondary">
                                                        <i class="fas fa-arrow-left"></i> Kembali ke Semua Data
                                                    </a>
                                                @else
                                                    <i class="fas fa-car fa-3x text-muted mb-3"></i>
                                                    <h5 class="text-muted">Tidak ada data mobil</h5>
                                                    <p class="text-muted mb-3">Belum ada mobil yang terdaftar dalam sistem</p>
                                                    <a href="{{ route('mobils.create') }}" class="btn btn-primary">
                                                        <i class="fas fa-plus"></i> Tambah Mobil Pertama
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Improved Pagination Section -->
                    @if($mobils->hasPages())
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <span class="text-muted">
                                        Total Keseluruhan: {{ $mobils->total() }} data
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-end">
                                    <nav aria-label="Pagination Navigation">
                                        <ul class="pagination pagination-sm mb-0">
                                            {{-- Previous Page Link --}}
                                            @if ($mobils->onFirstPage())
                                                <li class="page-item disabled">
                                                    <span class="page-link">
                                                        <i class="fas fa-chevron-left"></i>
                                                    </span>
                                                </li>
                                            @else
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $mobils->previousPageUrl() }}" rel="prev">
                                                        <i class="fas fa-chevron-left"></i>
                                                    </a>
                                                </li>
                                            @endif

                                            {{-- Pagination Elements --}}
                                            @foreach ($mobils->getUrlRange(1, $mobils->lastPage()) as $page => $url)
                                                @if ($page == $mobils->currentPage())
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
                                            @if ($mobils->hasMorePages())
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $mobils->nextPageUrl() }}" rel="next">
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

<script src="{{asset('assets/js/mobil.js')}}"></script>

@endsection