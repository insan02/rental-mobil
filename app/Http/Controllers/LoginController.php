<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        return view('login.index');
    }
    
    public function proses(Request $request)
    {
        $credential = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'Email tidak boleh kosong',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password tidak boleh kosong',
        ]);
        
        if (Auth::attempt($credential)) {
            $request->session()->regenerate();
            // Cek role user setelah login
            $role = Auth::user()->role;
            if ($role === 'admin') {
                return redirect()->route('home'); // Akses dashboard
            } elseif ($role === 'customer') {
                return redirect()->route('transaksis.index'); // Akses transaksi langsung
            }
            // Jika role tidak dikenal, logout dan tolak akses
            Auth::logout();
            return redirect()->route('login')->with('scroll_to_login', true)->withErrors(['email' => 'Role tidak dikenali.']);
        }
        
        // Redirect back dengan anchor ke section login
        return back()->with('scroll_to_login', true)->withErrors([
            'email' => 'Autentikasi Gagal! Email atau password salah.',
        ])->onlyInput('email');
    }
    
    public function keluar(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}