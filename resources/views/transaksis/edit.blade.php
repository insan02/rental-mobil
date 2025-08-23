@extends('layout.template')
@section('title', 'Edit Transaksi - RentCar')
    
@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-edit me-2"></i>Edit Transaksi
                            </h3>
                        </div>
                        <div class="col-auto">
                            <a href="{{ Auth::user()->role === 'admin' ? route('transaksis.admin-index') : route('transaksis.history') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Kembali
                            </a>
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

                    <form action="{{ route('transaksis.update', $transaksi) }}" method="POST" id="editForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Customer Data Section -->
                            <div class="col-lg-6">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-user me-2"></i>Data Pemesan
                                </h5>

                                @if(Auth::user()->role === 'admin')
                                    <div class="mb-3">
                                        <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control @error('nama') is-invalid @enderror" 
                                               id="nama" 
                                               name="nama" 
                                               value="{{ old('nama', $transaksi->nama) }}" 
                                               placeholder="Masukkan nama lengkap"
                                               required>
                                        @error('nama')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="ponsel" class="form-label">No. HP/Email <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control @error('ponsel') is-invalid @enderror" 
                                               id="ponsel" 
                                               name="ponsel" 
                                               value="{{ old('ponsel', $transaksi->ponsel) }}" 
                                               placeholder="Masukkan nomor HP atau email"
                                               required>
                                        @error('ponsel')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @else
                                    <!-- Read-only for customers -->
                                    <div class="mb-3">
                                        <label for="nama" class="form-label">Nama Lengkap</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="nama" 
                                               value="{{ $transaksi->nama }}" 
                                               readonly>
                                        <small class="text-muted">Data tidak dapat diubah</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="ponsel" class="form-label">No. HP/Email</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="ponsel" 
                                               value="{{ $transaksi->ponsel }}" 
                                               readonly>
                                        <small class="text-muted">Data tidak dapat diubah</small>
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <label for="alamat" class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('alamat') is-invalid @enderror" 
                                              id="alamat" 
                                              name="alamat" 
                                              rows="3" 
                                              placeholder="Masukkan alamat lengkap"
                                              required>{{ old('alamat', $transaksi->alamat) }}</textarea>
                                    @error('alamat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Booking Details Section -->
                            <div class="col-lg-6">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-car me-2"></i>Detail Sewa
                                </h5>

                                <div class="mb-3">
                                    <label for="mobil_id" class="form-label">Pilih Mobil <span class="text-danger">*</span></label>
                                    <select class="form-select @error('mobil_id') is-invalid @enderror" 
                                            id="mobil_id" 
                                            name="mobil_id" 
                                            required
                                            onchange="calculateTotal(); updateCarImage()">
                                        <option value="">Pilih Mobil</option>
                                        @foreach($mobils as $mobil)
                                        @php
    $archivedFotoPath = \App\Http\Controllers\MobilController::getArchivedFotoPath($mobil->foto);
    $fotoUrl = $archivedFotoPath ? asset('storage/' . $archivedFotoPath) : '';
@endphp

                                            <option value="{{ $mobil->id }}" 
        data-harga="{{ $mobil->harga }}"
        data-foto="{{ $fotoUrl }}"
        data-merek="{{ $mobil->merek }}"
        data-nopolisi="{{ $mobil->nopolisi }}"
        data-jenis="{{ $mobil->jenis }}"
        data-kapasitas="{{ $mobil->kapasitas }}"
        {{ old('mobil_id', $transaksi->mobil_id) == $mobil->id ? 'selected' : '' }}>
    {{ $mobil->merek }} - {{ $mobil->nopolisi }} (Rp {{ number_format($mobil->harga, 0, ',', '.') }}/hari)
</option>
                                        @endforeach
                                    </select>
                                    @error('mobil_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Car Details Card -->
                                <div class="card bg-light border-0 mb-3" id="car-details-card">
                                    <div class="card-body">
                                        <h6 class="text-primary mb-3">
                                            <i class="fas fa-info-circle me-1"></i>Detail Mobil Terpilih
                                        </h6>
                                        
                                        <div class="text-center mb-3">
                                            <img id="car-image" 
                                                 src="{{ $transaksi->mobil->foto ? asset('storage/' . $transaksi->mobil->foto) : asset('images/no-car.png') }}" 
                                                 alt="Foto Mobil" 
                                                 class="img-fluid rounded shadow-sm" 
                                                 style="max-height: 130px; object-fit: cover; width: 60%;"
                                                 onerror="this.src='{{ asset('images/no-car.png') }}'; this.alt='Foto tidak tersedia';">
                                        </div>

                                        <div class="row">
                                            <div class="col-6">
                                                <small class="text-muted">Merek:</small><br>
                                                <span id="car-merek" class="fw-bold">{{ $transaksi->mobil->merek ?? '-' }}</span>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">No. Polisi:</small><br>
                                                <span id="car-nopolisi" class="fw-bold">{{ $transaksi->mobil->nopolisi ?? '-' }}</span>
                                            </div>
                                            <div class="col-6 mt-2">
                                                <small class="text-muted">Jenis:</small><br>
                                                <span id="car-jenis" class="badge bg-info">{{ $transaksi->mobil->jenis ?? '-' }}</span>
                                            </div>
                                            <div class="col-6 mt-2">
                                                <small class="text-muted">Kapasitas:</small><br>
                                                <span id="car-kapasitas" class="fw-bold">{{ $transaksi->mobil->kapasitas ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Improved Date Section for Edit Form -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="tgl_pesan" class="form-label">Tanggal Sewa <span class="text-danger">*</span></label>
                                        <input type="date" 
                                            class="form-control @error('tgl_pesan') is-invalid @enderror" 
                                            id="tgl_pesan" 
                                            name="tgl_pesan" 
                                            value="{{ old('tgl_pesan', $transaksi->tgl_pesan ? date('Y-m-d', strtotime($transaksi->tgl_pesan)) : '') }}" 
                                            min="{{ date('Y-m-d') }}"
                                            required>
                                        @error('tgl_pesan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Data saat ini: {{ $transaksi->tgl_pesan ? date('d/m/Y', strtotime($transaksi->tgl_pesan)) : 'Belum diset' }}</small>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="lama" class="form-label">Lama Sewa (hari) <span class="text-danger">*</span></label>
                                        <input type="number" 
                                            class="form-control @error('lama') is-invalid @enderror" 
                                            id="lama" 
                                            name="lama" 
                                            value="{{ old('lama', $transaksi->lama) }}" 
                                            min="1" 
                                            max="30"
                                            required>
                                        @error('lama')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Data saat ini: {{ $transaksi->lama }} hari</small>
                                    </div>
                                </div>

                                <!-- Return Date Display -->
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Kembali</label>
                                    <input type="text" 
                                        class="form-control bg-light" 
                                        id="tgl_kembali_display" 
                                        readonly
                                        value="{{ $transaksi->tgl_pesan && $transaksi->lama ? date('l, d F Y', strtotime($transaksi->tgl_pesan . ' + ' . $transaksi->lama . ' days')) : '' }}">
                                    <small class="text-muted">
                                        Dihitung otomatis berdasarkan tanggal sewa + lama sewa
                                        @if($transaksi->tgl_pesan && $transaksi->lama)
                                            <br>Tanggal kembali saat ini: {{ date('d/m/Y', strtotime($transaksi->tgl_pesan . ' + ' . $transaksi->lama . ' days')) }}
                                        @endif
                                    </small>
                                </div>

                                @if(Auth::user()->role === 'admin')
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                        <select class="form-select @error('status') is-invalid @enderror" 
                                                id="status" 
                                                name="status" 
                                                required>
                                            @foreach($transaksi->getAvailableStatusOptions() as $value => $label)
                                                <option value="{{ $value }}" 
                                                        {{ old('status', $transaksi->status) == $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @else
                                    <!-- Show current status for customers -->
                                    <div class="mb-3">
                                        <label class="form-label">Status Saat Ini</label>
                                        <div class="form-control bg-light">
                                            @php
                                                $statusClass = '';
                                                switch($transaksi->status) {
                                                    case 'Wait': $statusClass = 'warning'; break;
                                                    case 'Approved': $statusClass = 'success'; break;
                                                    case 'Rejected': $statusClass = 'danger'; break;
                                                    case 'Ongoing': $statusClass = 'info'; break;
                                                    case 'Selesai': $statusClass = 'primary'; break;
                                                    case 'Terlambat': $statusClass = 'danger'; break;
                                                }
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">{{ $transaksi->status }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Total Calculation -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5 class="card-title text-primary">
                                            <i class="fas fa-calculator me-2"></i>Rincian Biaya
                                        </h5>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>Harga per hari:</strong><br>
                                                <span id="harga-per-hari" class="text-success">Rp {{ number_format($transaksi->mobil->harga ?? 0, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Lama sewa:</strong><br>
                                                <span id="lama-sewa">{{ $transaksi->lama }}</span> hari
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Total saat ini:</strong><br>
                                                <span class="text-muted">Rp {{ number_format($transaksi->total, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Total baru:</strong><br>
                                                <span id="total-biaya" class="text-primary fw-bold">Rp {{ number_format($transaksi->total, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ Auth::user()->role === 'admin' ? route('transaksis.admin-index') : route('transaksis.history') }}" 
                               class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Update Transaksi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{asset('assets/js/transaksi.js')}}"></script>

@endsection