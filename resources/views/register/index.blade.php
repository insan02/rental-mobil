<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>RentCar - Daftar Akun Baru</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="rental mobil, sewa mobil, car rental, daftar akun" name="keywords">
    <meta content="Daftar akun baru di platform rental mobil terpercaya dengan berbagai pilihan kendaraan berkualitas" name="description">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/img/ic.png') }}" type="image/png">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="{{asset('assets/css/register.css')}}" rel="stylesheet">
</head>

<body>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-transparent">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#home">
                <img src="{{ asset('assets/img/ic.png') }}" alt="RentCar Logo" width="30" height="30" class="me-2">
                RentCar
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}#login">Masuk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#register">Daftar</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Landing Section -->
    <section class="landing-section" id="home">
        <!-- Floating Car Icons -->
        <div class="floating-shapes">
            <div class="car-icon"><i class="fas fa-car"></i></div>
            <div class="car-icon"><i class="fas fa-car-side"></i></div>
            <div class="car-icon"><i class="fas fa-truck"></i></div>
            <div class="car-icon"><i class="fas fa-bus"></i></div>
            <div class="car-icon"><i class="fas fa-taxi"></i></div>
        </div>

        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">
                    <img src="{{ asset('assets/img/ic.png') }}" alt="RentCar Logo" width="80" height="80" class="me-3">
                    <span class="text-black">RentCar</span>
                </h1>

                <p class="hero-subtitle">Bergabunglah dengan ribuan pengguna yang telah mempercayai layanan rental mobil terbaik di Indonesia</p>
                

                <!-- Feature Grid -->
                <div class="feature-grid">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-gift"></i>
                        </div>
                        <h3 class="feature-title">Bonus Member Baru</h3>
                        <p class="feature-description">Dapatkan diskon 20% untuk rental pertama Anda sebagai member baru</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <h3 class="feature-title">Program Loyalitas</h3>
                        <p class="feature-description">Kumpulkan poin setiap rental dan tukar dengan diskon menarik</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h3 class="feature-title">Support Premium</h3>
                        <p class="feature-description">Akses prioritas ke customer service dan bantuan darurat 24/7</p>
                    </div>
                </div>


            </div>
        </div>
    </section>

    <section class="register-section" id="register">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="register-container">
                    <div class="brand-section">
                        <a href="#home" class="brand-logo loading-pulse">
                            <img src="{{ asset('assets/img/ic.png') }}" alt="RentCar Logo" width="50" height="50" class="me-2">
                            <span class="text-black">RentCar</span>
                        </a>
                        <h2 class="register-title">Daftar Akun</h2>
                        <p class="register-subtitle">Bergabunglah dengan ribuan pengguna yang telah mempercayai layanan kami</p>
                    </div>

                    <!-- Display All Errors -->
                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Terdapat kesalahan:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Success Message -->
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        Lengkapi data diri untuk membuat akun rental mobil
                    </div>

                    <form action="{{ route('register.store') }}" method="post" id="registerForm">
                        @csrf

                        <div class="form-floating mb-3">
                            <input type="text" 
                                   name="name" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="floatingName" 
                                   placeholder="Nama Lengkap" 
                                   value="{{ old('name') }}" 
                                   required>
                            <label for="floatingName">
                                <i class="fas fa-user me-2"></i>Nama Lengkap
                            </label>
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input type="email" 
                                   name="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="floatingEmail" 
                                   placeholder="nama@gmail.com" 
                                   value="{{ old('email') }}" 
                                   pattern="[a-zA-Z0-9._%+-]+@gmail\.com$"
                                   title="Email harus menggunakan domain @gmail.com"
                                   required>
                            <label for="floatingEmail">
                                <i class="fas fa-envelope me-2"></i>Alamat Email
                            </label>
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="form-text">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Hanya email dengan domain @gmail.com yang diterima
                                </small>
                            </div>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="tel" 
                                   name="nohp" 
                                   class="form-control @error('nohp') is-invalid @enderror" 
                                   id="floatingPhone" 
                                   placeholder="08xxxxxxxxxx" 
                                   value="{{ old('nohp') }}" 
                                   pattern="[0-9]{10,13}"
                                   title="Nomor HP harus berupa angka 10-13 digit"
                                   required>
                            <label for="floatingPhone">
                                <i class="fas fa-phone me-2"></i>Nomor HP
                            </label>
                            @error('nohp')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="form-text">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Contoh: 081234567890
                                </small>
                            </div>
                        </div>

                        <div class="form-floating mb-4">
                            <input type="password" 
                                   name="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="floatingPassword" 
                                   placeholder="Password" 
                                   minlength="8"
                                   required>
                            <label for="floatingPassword">
                                <i class="fas fa-lock me-2"></i>Kata Sandi
                            </label>
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="form-text">
                                <small class="text-muted">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    Minimal 8 karakter untuk keamanan akun
                                </small>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-register btn-success w-100 py-3 mb-4">
                            <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
                        </button>
                        
                        <div class="login-link">
                            <p class="mb-2">Sudah punya akun?</p>
                            <a href="{{ route('login') }}#login" class="fw-bold">Masuk Disini - Langsung Akses!</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

    <!-- Additional Info Section -->
    <section class="info-section py-5" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="fw-bold text-dark mb-3">Keuntungan Menjadi Member RentCar</h2>
                    <p class="text-muted fs-5">Nikmati berbagai benefit eksklusif untuk member</p>
                </div>
            </div>
            
            <div class="row g-4 justify-content-center">
                <div class="col-md-3 col-sm-6">
                    <div class="text-center p-4 bg-white rounded-4 shadow-sm h-100">
                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-percentage fs-2 text-success"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Diskon Khusus</h5>
                        <p class="text-muted small">Proses booking lebih cepat dengan data tersimpan otomatis</p>
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <div class="text-center p-4 bg-white rounded-4 shadow-sm h-100">
                        <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-crown fs-2 text-warning"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Priority Service</h5>
                        <p class="text-muted small">Layanan prioritas dan upgrade kendaraan gratis</p>
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <div class="text-center p-4 bg-white rounded-4 shadow-sm h-100">
                        <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-shield-alt fs-2 text-info"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Proteksi Penuh</h5>
                        <p class="text-muted small">Asuransi komprehensif dan jaminan kendaraan pengganti</p>
                    </div>
                </div>
            </div>
            
            <div class="row mt-5">
                <div class="col-12">
                    <div class="bg-success bg-opacity-10 rounded-4 p-4 text-center">
                        <div class="row align-items-center">
                            <div class="col-md-8 text-md-start text-center">
                                <h4 class="fw-bold text-success mb-2">
                                    <i class="fas fa-gift me-2"></i>
                                    Promo Spesial Member Baru!
                                </h4>
                                <p class="text-muted mb-0">Daftar sekarang dan dapatkan voucher rental senilai Rp 100.000</p>
                            </div>
                            <div class="col-md-4 text-md-end text-center mt-3 mt-md-0">
                                <a href="#register" class="btn btn-success btn-lg rounded-pill px-4">
                                    <i class="fas fa-user-plus me-2"></i>
                                    Daftar Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('assets/img/ic.png') }}" alt="RentCar Logo" width="30" height="30" class="me-2">
                        <span class="text-white fs-4 fw-bold">RentCar</span>
                    </div>
                    <p class="text-light-emphasis">Platform rental mobil terpercaya dengan layanan profesional dan armada lengkap untuk kebutuhan perjalanan Anda.</p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-white-50 fs-5"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-white-50 fs-5"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white-50 fs-5"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white-50 fs-5"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5 class="fw-bold mb-3">Layanan</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Rental Harian</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Rental Mingguan</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Rental Bulanan</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="fw-bold mb-3">Armada</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Sedan</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">SUV</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">MPV</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="fw-bold mb-3">Kontak</h5>
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-map-marker-alt me-3 text-success"></i>
                        <span class="text-white-50">Padang, Indonesia</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-phone me-3 text-success"></i>
                        <span class="text-white-50">0812-3456-7890</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-envelope me-3 text-success"></i>
                        <span class="text-white-50">info@rentcar.co.id</span>
                    </div>
                </div>
            </div>
            
            <hr class="my-4 border-secondary">
            
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="text-white-50 mb-0">&copy; 2025 RentCar. Semua hak dilindungi.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="#" class="text-white-50 text-decoration-none me-3">Syarat & Ketentuan</a>
                    <a href="#" class="text-white-50 text-decoration-none">Kebijakan Privasi</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <div class="back-to-top" onclick="scrollToTop()">
        <i class="fas fa-arrow-up"></i>
    </div>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="{{asset('assets/js/register.js')}}"></script>

    <script>
        @if(session('scroll_to_register') || $errors->any())
            scrollToLoginSection();
        @endif
    </script>
</body>

