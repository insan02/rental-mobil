<?php

use App\Http\Controllers\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MobilController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransaksiController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class,'index'])->name('home')->middleware('auth');

Route::get('register', [RegisterController::class, 'index'])->name('register');
Route::post('register', [RegisterController::class, 'store'])->name('register.store');
Route::get('login', [LoginController::class, 'index'])->name('login');
Route::post('login', [LoginController::class, 'proses'])->name('login.proses');
Route::get('login/keluar', [LoginController::class, 'keluar'])->name('login.keluar');

// Admin only (kecuali transaksi)
Route::middleware(['role:admin'])->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('mobils', MobilController::class);
    // Fixed: Change method name to valid PHP method name
    Route::get('/admin/transaksis', [TransaksiController::class, 'adminIndex'])->name('transaksis.admin-index');
    Route::patch('/transaksis/{transaksi}/update-status', [TransaksiController::class, 'updateStatus'])->name('transaksis.update-status');
    Route::get('/transaksis/history/cetak', [TransaksiController::class, 'cetakPdf'])->name('transaksis.cetak');
});

// Customer only (transaksi dengan custom routes)
Route::middleware(['role:customer'])->group(function () {
    Route::get('/transaksis', [TransaksiController::class, 'index'])->name('transaksis.index');
    Route::get('/transaksis/create/{mobil_id?}', [TransaksiController::class, 'create'])->name('transaksis.create');
    Route::post('/transaksis', [TransaksiController::class, 'store'])->name('transaksis.store');
    Route::get('/transaksis/{transaksi}', [TransaksiController::class, 'show'])->name('transaksis.show');
    Route::get('/transaksis/{transaksi}/edit', [TransaksiController::class, 'edit'])->name('transaksis.edit');
    Route::put('/transaksis/{transaksi}', [TransaksiController::class, 'update'])->name('transaksis.update');
    
});

// Both admin and customer can access history and update status
Route::middleware(['role:customer,admin'])->group(function () {
    Route::post('/check-archived-foto', function(\Illuminate\Http\Request $request) {
    $fotoPath = $request->input('foto_path');
    $archivedPath = MobilController::getArchivedFotoPath($fotoPath);
    
    return response()->json([
        'foto_url' => $archivedPath ? asset('storage/' . $archivedPath) : null,
        'exists' => !empty($archivedPath)
    ]);
})->name('check-archived-foto');
    Route::get('/transaksis-history', [TransaksiController::class, 'history'])->name('transaksis.history');
    Route::patch('transaksis/{transaksi}/status', [TransaksiController::class, 'updateStatus'])->name('transaksis.updateStatus');
    Route::delete('/transaksis/{transaksi}', [TransaksiController::class, 'destroy'])->name('transaksis.destroy');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

});