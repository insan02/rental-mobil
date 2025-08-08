<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Mobil;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
{
    $totalMobil = Mobil::count();
    $totalUsers = User::count();
    
    // Transaksi selesai (termasuk soft delete)
    $transaksiSelesai = Transaksi::withTrashed()
        ->where('status', Transaksi::STATUS_SELESAI)
        ->count();
    
    // Total pendapatan dari transaksi selesai (termasuk soft delete)
    $totalPendapatan = Transaksi::withTrashed()
        ->where('status', Transaksi::STATUS_SELESAI)
        ->sum('total');
    
    return view('home', compact(
        'totalMobil', 
        'totalUsers',
        'transaksiSelesai',
        'totalPendapatan'
    ));
}

}