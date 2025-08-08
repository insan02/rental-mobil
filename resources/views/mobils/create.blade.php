@extends('layout.template')
@section('title', 'Kelola Mobil - RentCar')
    
@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tambah Data Mobil</h3>
                </div>

                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('mobils.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <!-- Merek (tukar posisi jadi kiri) -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="merek" class="form-label">Merek <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('merek') is-invalid @enderror" 
                                           id="merek" 
                                           name="merek" 
                                           value="{{ old('merek') }}" 
                                           placeholder="Masukkan merek mobil"
                                           required>
                                    @error('merek')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- No Polisi (tukar posisi jadi kanan) -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nopolisi" class="form-label">No Polisi <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('nopolisi') is-invalid @enderror" 
                                           id="nopolisi" 
                                           name="nopolisi" 
                                           value="{{ old('nopolisi') }}" 
                                           placeholder="Masukkan no polisi (e.g., B 1234 ABC)"
                                           required>
                                    @error('nopolisi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="jenis" class="form-label">Jenis <span class="text-danger">*</span></label>
                                    <select class="form-select @error('jenis') is-invalid @enderror" 
                                            id="jenis" 
                                            name="jenis" 
                                            required>
                                        <option value="">Pilih Jenis Mobil</option>
                                        <option value="Sedan" {{ old('jenis') == 'Sedan' ? 'selected' : '' }}>Sedan</option>
                                        <option value="MPV" {{ old('jenis') == 'MPV' ? 'selected' : '' }}>MPV</option>
                                        <option value="SUV" {{ old('jenis') == 'SUV' ? 'selected' : '' }}>SUV</option>
                                    </select>
                                    @error('jenis')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="kapasitas" class="form-label">Kapasitas <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('kapasitas') is-invalid @enderror" 
                                           id="kapasitas" 
                                           name="kapasitas" 
                                           value="{{ old('kapasitas') }}" 
                                           placeholder="Masukkan kapasitas (e.g., 5 Penumpang)"
                                           required>
                                    @error('kapasitas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="harga" class="form-label">Harga (per hari) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" 
                                               class="form-control @error('harga') is-invalid @enderror" 
                                               id="harga" 
                                               name="harga" 
                                               value="{{ old('harga') }}" 
                                               placeholder="Masukkan harga per hari"
                                               min="0"
                                               required>
                                        @error('harga')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="foto" class="form-label">Foto Mobil</label>
                                    <input type="file" 
                                           class="form-control @error('foto') is-invalid @enderror" 
                                           id="foto" 
                                           name="foto" 
                                           accept="image/*"
                                           onchange="previewImage(event)">
                                    <div class="form-text">File types: JPEG, PNG, JPG, GIF. Max size: 2MB</div>
                                    @error('foto')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    
                                    <!-- Image Preview Container -->
                                    <div id="imagePreviewContainer" class="mt-3" style="display: none;">
                                        <div class="card" style="max-width: 300px;">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <small class="text-muted">Preview Foto</small>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removePreview()">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            <div class="card-body p-2">
                                                <img id="imagePreview" 
                                                     src="" 
                                                     alt="Preview" 
                                                     class="img-fluid rounded" 
                                                     style="max-height: 200px; width: 100%; object-fit: cover;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('mobils.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
