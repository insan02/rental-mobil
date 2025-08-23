<?php

namespace App\Http\Controllers;

use App\Models\Mobil;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


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
            'user_id' => Auth::id(),
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
            
            // Store in mobils folder
            $mobilsPath = $file->storeAs('mobils', $filename, 'public');
            $mobilData['foto'] = $mobilsPath;
            
            // Backup to archived_fotomobil folder
            $this->backupFotoToArchive($file, $filename);
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
            'nopolisi' => $request->input('nopolisi'),
            'merek' => $request->input('merek'),
            'jenis' => $request->input('jenis'),
            'kapasitas' => $request->input('kapasitas'),
            'harga' => $request->input('harga'),
        ];

        // Handle file upload
        if ($request->hasFile('foto')) {
            // Delete old file from mobils folder if exists
            $oldFoto = $mobil->getAttribute('foto');
            if (!empty($oldFoto) && Storage::disk('public')->exists($oldFoto)) {
                Storage::disk('public')->delete($oldFoto);
            }

            $file = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Store in mobils folder
            $mobilsPath = $file->storeAs('mobils', $filename, 'public');
            $mobilData['foto'] = $mobilsPath;
            
            // Backup to archived_fotomobil folder
            $this->backupFotoToArchive($file, $filename);
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
        // Get foto path before deletion
        $fotoPath = $mobil->getAttribute('foto');
        
        // If foto exists, backup to archive before deleting
        if (!empty($fotoPath) && Storage::disk('public')->exists($fotoPath)) {
            // Extract filename from path
            $filename = basename($fotoPath);
            
            // Copy to archived_fotomobil folder if not already exists
            $archivePath = 'archived_fotomobil/' . $filename;
            if (!Storage::disk('public')->exists($archivePath)) {
                Storage::disk('public')->copy($fotoPath, $archivePath);
            }
            
            // Delete from mobils folder
            Storage::disk('public')->delete($fotoPath);
        }

        $mobil->delete();

        return redirect()->route('mobils.index')
                        ->with('success', 'Mobil deleted successfully.');
    }

    /**
     * Backup foto to archived_fotomobil folder
     */
    private function backupFotoToArchive($file, $filename)
    {
        try {
            // Store backup copy in archived_fotomobil folder
            $file->storeAs('archived_fotomobil', $filename, 'public');
        } catch (\Exception $e) {
            // Log error but don't stop the main process
            Log::error('Failed to backup foto to archive: ' . $e->getMessage());
        }
    }

    /**
     * Get archived foto path for a given filename
     */
    public static function getArchivedFotoPath($fotoPath)
    {
        if (empty($fotoPath)) {
            return null;
        }

        $filename = basename($fotoPath);
        $archivePath = 'archived_fotomobil/' . $filename;
        
        // Check if archived version exists
        if (Storage::disk('public')->exists($archivePath)) {
            return $archivePath;
        }
        
        // Check if original still exists
        if (Storage::disk('public')->exists($fotoPath)) {
            return $fotoPath;
        }
        
        return null;
    }
}