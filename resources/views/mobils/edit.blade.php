@extends('layout.template')
@section('title', 'Kelola Mobil - RentCar')
    
@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Data Mobil</h3>
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

                    <form id="editMobilForm" action="{{ route('mobils.update', $mobil) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="merek" class="form-label">Merek <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('merek') is-invalid @enderror" 
                                           id="merek" 
                                           name="merek" 
                                           value="{{ old('merek') ?? $mobil->merek }}" 
                                           placeholder="Masukkan merek mobil"
                                           required>
                                    @error('merek')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nopolisi" class="form-label">No Polisi <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('nopolisi') is-invalid @enderror" 
                                           id="nopolisi" 
                                           name="nopolisi" 
                                           value="{{ old('nopolisi') ?? $mobil->nopolisi }}" 
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
                                        <option value="Sedan" {{ (old('jenis') ?? $mobil->jenis) == 'Sedan' ? 'selected' : '' }}>Sedan</option>
                                        <option value="MPV" {{ (old('jenis') ?? $mobil->jenis) == 'MPV' ? 'selected' : '' }}>MPV</option>
                                        <option value="SUV" {{ (old('jenis') ?? $mobil->jenis) == 'SUV' ? 'selected' : '' }}>SUV</option>
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
                                           value="{{ old('kapasitas') ?? $mobil->kapasitas }}" 
                                           placeholder="Masukkan kapasitas (e.g., 5 )"
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
                                               value="{{ old('harga') ?? $mobil->harga }}" 
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
                                </div>

                                <!-- Photo Section -->
                                <div class="row">
                                    <div class="col-6">
                                        @if($mobil->foto)
                                            <div class="mb-3">
                                                <label class="form-label">Foto Saat Ini</label>
                                                <div>
                                                    <img src="{{ asset('storage/' . $mobil->foto) }}" 
                                                         alt="{{ $mobil->merek }}" 
                                                         class="img-thumbnail" 
                                                         style="max-width: 200px; max-height: 150px;">
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="col-6">
                                        <!-- Preview Container -->
                                        <div id="imagePreviewContainer" style="display: none;" class="mb-3">
                                            <label class="form-label">Foto Baru</label>
                                            <div class="position-relative">
                                                <img id="imagePreview" 
                                                     src="" 
                                                     alt="Preview" 
                                                     class="img-thumbnail" 
                                                     style="max-width: 200px; max-height: 150px;">
                                                <button type="button" 
                                                        class="btn btn-danger btn-sm position-absolute" 
                                                        style="top: 5px; right: 5px; padding: 2px 6px; font-size: 10px;"
                                                        onclick="removePreview()"
                                                        title="Remove Preview">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('mobils.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="button" id="btnUpdate" class="btn btn-primary">
                                <i class="fas fa-save"></i> Perbarui
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{asset('assets/js/mobil.js')}}"></script>

@endsection