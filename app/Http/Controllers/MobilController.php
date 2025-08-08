<?php

namespace App\Http\Controllers;

use App\Models\Mobil;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class MobilController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10); // Default 10 items per page
        $search = $request->get('search');
        $jenisFilter = $request->get('jenis_filter');
        
        $query = Mobil::with('user');
        
        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nopolisi', 'LIKE', "%{$search}%")
                  ->orWhere('merek', 'LIKE', "%{$search}%")
                  ->orWhere('jenis', 'LIKE', "%{$search}%")
                  ->orWhere('kapasitas', 'LIKE', "%{$search}%")
                  ->orWhere('harga', 'LIKE', "%{$search}%");
            });
        }
        
        $mobils = $query->latest()->paginate($perPage);
        
        return view('mobils.index', compact('mobils'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('mobils.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nopolisi' => 'required|string|max:255|unique:mobils',
            'merek' => 'required|string|max:255',
            'jenis' => 'required|string|in:Sedan,MPV,SUV',
            'kapasitas' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $mobilData = [
            'user_id' => Auth::id(), // Otomatis mengisi dengan ID user yang sedang login
            'nopolisi' => $request->input('nopolisi'),
            'merek' => $request->input('merek'),
            'jenis' => $request->input('jenis'),
            'kapasitas' => $request->input('kapasitas'),
            'harga' => $request->input('harga'),
        ];

        // Handle file upload
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('mobils', $filename, 'public');
            $mobilData['foto'] = $path;
        }

        Mobil::create($mobilData);

        return redirect()->route('mobils.index')
                        ->with('success', 'Mobil created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Mobil $mobil)
    {
        $mobil->load('user');
        return view('mobils.show', compact('mobil'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mobil $mobil)
    {
        return view('mobils.edit', compact('mobil'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Mobil $mobil)
    {
        $request->validate([
            'nopolisi' => 'required|string|max:255|unique:mobils,nopolisi,' . $mobil->getKey(),
            'merek' => 'required|string|max:255',
            'jenis' => 'required|string|in:Sedan,MPV,SUV',
            'kapasitas' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $mobilData = [
            // user_id tidak diubah saat update, tetap menggunakan yang sudah ada
            'nopolisi' => $request->input('nopolisi'),
            'merek' => $request->input('merek'),
            'jenis' => $request->input('jenis'),
            'kapasitas' => $request->input('kapasitas'),
            'harga' => $request->input('harga'),
        ];

        // Handle file upload
        if ($request->hasFile('foto')) {
            // Delete old file if exists
            $oldFoto = $mobil->getAttribute('foto');
            if (!empty($oldFoto) && Storage::disk('public')->exists($oldFoto)) {
                Storage::disk('public')->delete($oldFoto);
            }

            $file = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('mobils', $filename, 'public');
            $mobilData['foto'] = $path;
        }

        $mobil->update($mobilData);

        return redirect()->route('mobils.index')
                        ->with('success', 'Mobil updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mobil $mobil)
    {
        // Delete associated file - using getAttribute to safely access the foto property
        $fotoPath = $mobil->getAttribute('foto');
        if (!empty($fotoPath) && Storage::disk('public')->exists($fotoPath)) {
            Storage::disk('public')->delete($fotoPath);
        }

        $mobil->delete();

        return redirect()->route('mobils.index')
                        ->with('success', 'Mobil deleted successfully.');
    }
}