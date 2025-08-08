@extends('layout.template')
@section('title', 'Profil - RentCar')
    
@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Profil Pengguna</h3>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="editProfilForm" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">

                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Profile Photo Section -->
                            <div class="col-md-4 text-center mb-4">
                                <div class="mb-3">
                                    @if(auth()->user()->foto && Storage::disk('public')->exists(auth()->user()->foto))
                                        <img src="{{ Storage::url(auth()->user()->foto) }}" 
                                             alt="Profile Photo" 
                                             class="img-fluid rounded-circle mb-3" 
                                             style="width: 200px; height: 200px; object-fit: cover; border: 3px solid #dee2e6;">
                                    @else
                                        <img src="{{ asset('assets/img/user.png') }}" 
                                             alt="Profile Photo" 
                                             class="img-fluid rounded-circle mb-3" 
                                             style="width: 200px; height: 200px; object-fit: cover; border: 3px solid #dee2e6;">
                                    @endif
                                    
                                    <div class="mt-3">
                                        <input type="file" 
                                               class="form-control @error('foto') is-invalid @enderror" 
                                               id="foto" 
                                               name="foto" 
                                               accept="image/*"
                                               onchange="previewProfileImage(event)">
                                        <div class="form-text">File types: JPEG, PNG, JPG, GIF. Max size: 2MB</div>
                                        @error('foto')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                            </div>

                            <!-- Profile Form Section -->
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" 
                                                   name="name" 
                                                   value="{{ old('name') ?? auth()->user()->name }}" 
                                                   placeholder="Masukkan nama lengkap"
                                                   required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                            <input type="email" 
                                                   class="form-control @error('email') is-invalid @enderror" 
                                                   id="email" 
                                                   name="email" 
                                                   value="{{ old('email') ?? auth()->user()->email }}" 
                                                   placeholder="Masukkan alamat email"
                                                   required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="nohp" class="form-label">No Handphone <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   class="form-control @error('nohp') is-invalid @enderror" 
                                                   id="nohp" 
                                                   name="nohp" 
                                                   value="{{ old('nohp') ?? auth()->user()->nohp }}" 
                                                   placeholder="Masukkan nomor handphone"
                                                   required>
                                            @error('nohp')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Change Password Section -->
                                <hr class="my-4">
                                <h5 class="mb-3">Ubah Password</h5>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Kosongkan field password jika tidak ingin mengubah password.
                                </div>

                                <div class="row">
                                        <div class="mb-3">
                                            <label for="current_password" class="form-label">Password Saat Ini</label>
                                            <div class="input-group">
                                                <input type="password" 
                                                       class="form-control @error('current_password') is-invalid @enderror" 
                                                       id="current_password" 
                                                       name="current_password" 
                                                       placeholder="Masukkan password saat ini">
                                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                                    <i class="fas fa-eye" id="current_password_icon"></i>
                                                </button>
                                                @error('current_password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    

                                    
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Password Baru</label>
                                            <div class="input-group">
                                                <input type="password" 
                                                       class="form-control @error('password') is-invalid @enderror" 
                                                       id="password" 
                                                       name="password" 
                                                       placeholder="Masukkan password baru">
                                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                                    <i class="fas fa-eye" id="password_icon"></i>
                                                </button>
                                                @error('password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    
                            
                                        <div class="mb-3">
                                            <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                                            <div class="input-group">
                                                <input type="password" 
                                                       class="form-control" 
                                                       id="password_confirmation" 
                                                       name="password_confirmation" 
                                                       placeholder="Konfirmasi password baru">
                                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                                    <i class="fas fa-eye" id="password_confirmation_icon"></i>
                                                </button>
                                            </div>
                                        </div>
                                    
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <a href="{{ route('home') }}" class="btn btn-secondary me-2">Batal</a>
                                    <button type="button" id="btnUpdateProfil" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Perbarui Profil
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{asset('assets/js/profile.js')}}"></script>
@endsection