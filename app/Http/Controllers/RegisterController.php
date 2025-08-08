<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('register.index');
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:20',
            'email' => 'required|string|email|max:25|unique:users|ends_with:@gmail.com',
            'nohp' => 'required|string|max:13',
            'password' => 'required|string|min:8',
        ],
        [
            'name.required' => 'Nama tidak boleh kosong',
            'name.max' => 'Nama maksimal 20 karakter',
            'email.required' => 'Email tidak boleh kosong',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar, silakan gunakan email lain',
            'email.max' => 'Email maksimal 25 karakter',
            'email.ends_with' => 'Email harus menggunakan domain @gmail.com',
            'nohp.required' => 'Nomor HP tidak boleh kosong',
            'nohp.max' => 'Nomor HP maksimal 13 digit',
            'password.required' => 'Password tidak boleh kosong',
            'password.min' => 'Password minimal 8 karakter',
        ]);   
        
        User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'nohp' => $request->input('nohp'),
            'password' => Hash::make($request->input('password')),
            'role' => 'customer', // Default role
        ]);
        
        // Redirect ke login dengan pesan sukses dan auto scroll
        return redirect()->route('login')->with([
            'success' => 'Registrasi berhasil! Silakan login dengan akun baru Anda.',
            'scroll_to_login' => true
        ]);
    }
}