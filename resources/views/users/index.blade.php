@extends('layout.template')
@section('title', 'Kelola Customer - RentCar')
    
@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Data Customer</h3>
                </div>

                <div class="card-body">
                    {{-- Success Alert --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{!! session('success') !!}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Error Alert --}}
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>{!! session('error') !!}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

                    <!-- Search Section -->
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <form method="GET" action="{{ route('users.index') }}" class="d-flex">
                                <div class="input-group">
                                    <input type="text" 
                                           name="search" 
                                           class="form-control" 
                                           placeholder="Cari berdasarkan Nama, Email, atau No HP" 
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
                    @if(request('search'))
                        <div class="alert alert-info mb-3 d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-info-circle me-2"></i>
                                Menampilkan {{ $users->total() }} hasil 
                                untuk pencarian "<strong>{{ request('search') }}</strong>"
                                @if($users->total() == 0)
                                    <br><small class="text-muted">Coba gunakan kata kunci yang berbeda atau reset filter</small>
                                @endif
                            </div>
                            <!-- Tombol Reset -->
                            <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-sync-alt me-1"></i> Reset
                            </a>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>No HP</th>
                                    <th>Tanggal Daftar Akun</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $key => $user)
                                    <tr>
                                        <td>{{ $users->firstItem() + $key }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->nohp ?? '-' }}</td>
                                        <td>{{ $user->created_at->setTimezone('Asia/Jakarta')->locale('id')->isoFormat('DD MMMM YYYY, HH:mm') }} WIB</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <form action="{{ route('users.destroy', $user) }}" 
                                                      method="POST" class="d-inline form-delete">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" 
                                                            class="btn btn-danger btn-sm btn-delete" 
                                                            data-name="{{ $user->name }}"
                                                            data-email="{{ $user->email }}"
                                                            title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="d-flex flex-column align-items-center">
                                                @if(request('search'))
                                                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                                    <h5 class="text-muted">Tidak ada hasil pencarian</h5>
                                                    <p class="text-muted mb-3">
                                                        Tidak ditemukan customer yang sesuai dengan kriteria pencarian
                                                    </p>
                                                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                                        <i class="fas fa-arrow-left"></i> Kembali ke Semua Data
                                                    </a>
                                                @else
                                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                                    <h5 class="text-muted">Tidak ada data customer</h5>
                                                    <p class="text-muted mb-3">Belum ada customer yang terdaftar dalam sistem</p>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Improved Pagination Section -->
                    @if($users->hasPages())
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <span class="text-muted">
                                        Total Keseluruhan: {{ $users->total() }} data
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-end">
                                    <nav aria-label="Pagination Navigation">
                                        <ul class="pagination pagination-sm mb-0">
                                            {{-- Previous Page Link --}}
                                            @if ($users->onFirstPage())
                                                <li class="page-item disabled">
                                                    <span class="page-link">
                                                        <i class="fas fa-chevron-left"></i>
                                                    </span>
                                                </li>
                                            @else
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $users->previousPageUrl() }}" rel="prev">
                                                        <i class="fas fa-chevron-left"></i>
                                                    </a>
                                                </li>
                                            @endif

                                            {{-- Pagination Elements --}}
                                            @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                                                @if ($page == $users->currentPage())
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
                                            @if ($users->hasMorePages())
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $users->nextPageUrl() }}" rel="next">
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

<script src="{{asset('assets/js/user.js')}}"></script>

@endsection