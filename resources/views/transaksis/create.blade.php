@extends('layout.template')
@section('title', 'Booking Mobil - RentCar')
    
@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-calendar-check me-2"></i>Form Booking Mobil
                            </h3>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Terdapat kesalahan:</h6>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row">
                        <!-- Car Details -->
                        <div class="col-lg-5">
                            <div class="card bg-light border-0 h-100">
                                <div class="card-body">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-car me-2"></i>Detail Mobil
                                    </h5>
                                    
                                    <div class="text-center mb-3">
                                        <img id="selected-car-image"
                                             src="{{ $mobil->foto ? asset('storage/' . $mobil->foto) : asset('images/no-car.png') }}" 
                                             alt="{{ $mobil->merek }}" 
                                             class="img-fluid rounded shadow-sm" 
                                             style="max-height: 200px; object-fit: cover; width: 100%;"
                                             onerror="this.src='{{ asset('images/no-car.png') }}'; this.alt='Foto tidak tersedia';">
                                    </div>

                                    <table class="table table-sm">
                                        <tr>
                                            <td class="fw-bold">Merek:</td>
                                            <td id="selected-car-merek">{{ $mobil->merek }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">No. Polisi:</td>
                                            <td id="selected-car-nopolisi">{{ $mobil->nopolisi }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Jenis:</td>
                                            <td>
                                                <span id="selected-car-jenis" class="badge 
                                                    @if($mobil->jenis === 'Sedan') bg-info
                                                    @elseif($mobil->jenis === 'MPV') bg-warning
                                                    @elseif($mobil->jenis === 'SUV') bg-success
                                                    @else bg-secondary
                                                    @endif">
                                                    {{ $mobil->jenis }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Kapasitas:</td>
                                            <td id="selected-car-kapasitas">{{ $mobil->kapasitas }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Harga/hari:</td>
                                            <td class="text-success fw-bold" id="selected-car-harga">
                                                Rp {{ number_format($mobil->harga, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Booking Form -->
                        <div class="col-lg-7">
                            <form action="{{ route('transaksis.store') }}" method="POST" id="bookingForm">
                                @csrf
                                <input type="hidden" name="mobil_id" value="{{ $mobil->id }}" id="selected_mobil_id">
                                
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-user me-2"></i>Data Pemesan
                                </h5>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="nama" class="form-label">Nama Lengkap</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="nama" 
                                               name="nama" 
                                               value="{{ $user->name }}" 
                                               readonly>
                                        <small class="text-muted">Data diambil dari profil Anda</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="nohp" class="form-label">No. HP/Email</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="nohp" 
                                               name="nohp" 
                                               value="{{ $user->nohp ?? $user->email }}" 
                                               readonly>
                                        <small class="text-muted">Data diambil dari profil Anda</small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="alamat" class="form-label">
                                        Alamat Lengkap <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('alamat') is-invalid @enderror" 
                                              id="alamat" 
                                              name="alamat" 
                                              rows="3" 
                                              placeholder="Masukkan alamat lengkap untuk pengantaran/penjemputan"
                                              required>{{ old('alamat') }}</textarea>
                                    @error('alamat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <h5 class="text-primary mb-3 mt-4">
                                    <i class="fas fa-calendar-alt me-2"></i>Detail Sewa
                                </h5>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="tgl_pesan" class="form-label">
                                            Tanggal Sewa <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" 
                                               class="form-control @error('tgl_pesan') is-invalid @enderror" 
                                               id="tgl_pesan" 
                                               name="tgl_pesan" 
                                               value="{{ old('tgl_pesan') }}" 
                                               min="{{ date('Y-m-d') }}"
                                               required
                                               onchange="calculateReturnDate()">
                                        @error('tgl_pesan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="lama" class="form-label">
                                            Lama Sewa (hari) <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" 
                                               class="form-control @error('lama') is-invalid @enderror" 
                                               id="lama" 
                                               name="lama" 
                                               value="{{ old('lama', 1) }}" 
                                               min="1" 
                                               max="30"
                                               required
                                               onchange="calculateTotal(); calculateReturnDate()">
                                        @error('lama')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Return Date Display -->
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Kembali (Otomatis)</label>
                                    <input type="text" 
                                           class="form-control bg-light" 
                                           id="tgl_kembali_display" 
                                           readonly>
                                    <small class="text-muted">Dihitung otomatis berdasarkan tanggal sewa + lama sewa</small>
                                </div>

                                <!-- Total Calculation -->
                                <div class="alert alert-info">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <strong>Harga per hari:</strong> 
                                            <span class="text-success" id="harga-per-hari">Rp {{ number_format($mobil->harga, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="col-sm-6">
                                            <strong>Total Biaya:</strong> 
                                            <span class="text-primary fw-bold" id="totalBiaya">
                                                Rp {{ number_format($mobil->harga, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('transaksis.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i>Batal
                                    </a>
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-paper-plane me-2"></i>Submit Booking
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{asset('assets/js/transaksi.js')}}"></script>

@endsection