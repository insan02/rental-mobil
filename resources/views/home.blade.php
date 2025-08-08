@extends('layout.template')
@section('title', 'Home - RentCar')

@section('content')
<!-- Statistik Utama -->
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <!-- Total Mobil -->
        <div class="col-sm-6 col-lg-3">
            <div class="bg-light rounded shadow-sm d-flex align-items-center justify-content-between p-4">
                <div class="text-success text-center me-3">
                    <i class="fa fa-car fa-3x"></i>
                </div>
                <div class="text-end">
                    <p class="mb-1 fw-semibold">Total Mobil</p>
                    <h5 class="mb-0">{{ number_format($totalMobil) }}</h5>
                </div>
            </div>
        </div>

        <!-- Total Users -->
        <div class="col-sm-6 col-lg-3">
            <div class="bg-light rounded shadow-sm d-flex align-items-center justify-content-between p-4">
                <div class="text-warning text-center me-3">
                    <i class="fa fa-users fa-3x"></i>
                </div>
                <div class="text-end">
                    <p class="mb-1 fw-semibold">Total Customer</p>
                    <h5 class="mb-0">{{ number_format($totalUsers) }}</h5>
                </div>
            </div>
        </div>

        <!-- Transaksi Selesai -->
        <div class="col-sm-6 col-lg-3">
            <div class="bg-light rounded shadow-sm d-flex align-items-center justify-content-between p-4">
                <div class="text-primary text-center me-3">
                    <i class="fa fa-dollar-sign fa-3x"></i>
                </div>
                <div class="text-end">
                    <p class="mb-1 fw-semibold">Transaksi Selesai</p>
                    <h5 class="mb-0">{{ number_format($transaksiSelesai) }}</h5>
                </div>
            </div>
        </div>

        <!-- Total Pendapatan -->
        <div class="col-sm-6 col-lg-3">
            <div class="bg-light rounded shadow-sm d-flex align-items-center justify-content-between p-4">
                <div class="text-success text-center me-3">
                    <i class="fa fa-money-bill-wave fa-3x"></i>
                </div>
                <div class="text-end">
                    <p class="mb-1 fw-semibold">Total Pendapatan</p>
                    <h5 class="mb-0">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
