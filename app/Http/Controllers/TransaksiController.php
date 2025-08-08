<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Mobil;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;


class TransaksiController extends Controller
{
    /**
     * Display a listing of available cars for customers
     */
    public function index(Request $request)
{
    $search = $request->get('search');
    $jenisFilter = $request->get('jenis_filter');
    $perPage = $request->get('per_page', 6); // default 6 mobil per halaman

    $query = Mobil::query();

    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('merek', 'LIKE', "%{$search}%")
              ->orWhere('nopolisi', 'LIKE', "%{$search}%")
              ->orWhere('kapasitas', 'LIKE', "%{$search}%")
              ->orWhere('jenis', 'LIKE', "%{$search}%");
        });
    }

    if ($jenisFilter && $jenisFilter !== 'all') {
        $query->where('jenis', $jenisFilter);
    }

    $mobils = $query->latest()->paginate($perPage);

    // List jenis mobil untuk dropdown filter
    $jenisOptions = [
        'all' => 'Semua Jenis',
        'Sedan' => 'Sedan',
        'MPV' => 'MPV',
        'SUV' => 'SUV',
        'Pickup' => 'Pickup',
        'Minibus' => 'Minibus',
    ];

    return view('transaksis.index', compact('mobils', 'jenisOptions'));
}


    /**
     * Display all transactions for admin (separate method)
     */
    public function adminIndex(Request $request)
    {
        // Update late transactions before showing admin page
        $this->updateLateTransactions();
        
        $perPage = $request->get('per_page', 10); // Default 10 items per page
        $search = $request->get('search');
        $statusFilter = $request->get('status_filter');
        
        // Only for admin - show transactions that are not completed (exclude 'Selesai')
        $query = Transaksi::with(['user', 'mobil'])
                        ->where('status', '!=', Transaksi::STATUS_SELESAI);
        
        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'LIKE', "%{$search}%")
                ->orWhere('ponsel', 'LIKE', "%{$search}%")
                ->orWhere('alamat', 'LIKE', "%{$search}%")
                ->orWhere('total', 'LIKE', "%{$search}%")
                ->orWhereHas('mobil', function($mq) use ($search) {
                    $mq->where('merek', 'LIKE', "%{$search}%")
                        ->orWhere('nopolisi', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('user', function($uq) use ($search) {
                    $uq->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                });
            });
        }
        
        // Apply status filter
        if ($statusFilter && $statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }
        
        $transaksis = $query->latest()->paginate($perPage);
        
        // Get available status options for filter
        $statusOptions = [
            'all' => 'Semua Status',
            'Wait' => 'Menunggu',
            'Proses' => 'Diproses',
            'Terlambat' => 'Terlambat'
        ];
        
        return view('transaksis.admin-index', compact('transaksis', 'statusOptions'));
    }

    /**
     * Display transaction history for users
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->get('per_page', 10); // Default 10 items per page
        $search = $request->get('search');
        $statusFilter = $request->get('status_filter');
        $dateFilter = $request->get('date_filter');
        
        if ($user->role === 'admin') {
            // Update late transactions for admin
            $this->updateLateTransactions();
            
            // Admin sees only completed transactions (status = 'Selesai')
            $query = Transaksi::withTrashed()
                            ->with(['user', 'mobil'])
                            ->where('status', Transaksi::STATUS_SELESAI);
            
            // Admin search includes customer data
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('nama', 'LIKE', "%{$search}%")
                    ->orWhere('ponsel', 'LIKE', "%{$search}%")
                    ->orWhere('alamat', 'LIKE', "%{$search}%")
                    ->orWhere('total', 'LIKE', "%{$search}%")
                    ->orWhereHas('mobil', function($mq) use ($search) {
                        $mq->where('merek', 'LIKE', "%{$search}%")
                            ->orWhere('nopolisi', 'LIKE', "%{$search}%")
                            ->orWhere('jenis', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('user', function($uq) use ($search) {
                        $uq->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
                    });
                });
            }
            
            // Admin status options (for completed transactions, we can filter by return status)
            $statusOptions = [
                'all' => 'Semua Transaksi',
                'ontime' => 'Tepat Waktu',
                'late' => 'Terlambat',
                'early' => 'Lebih Awal'
            ];
            
            // Apply return status filter for admin
            if ($statusFilter && $statusFilter !== 'all') {
                $query->whereNotNull('tgl_kembali_aktual');
                if ($statusFilter === 'ontime') {
                    $query->whereRaw('DATE(tgl_kembali_aktual) = DATE(tgl_kembali)');
                } elseif ($statusFilter === 'late') {
                    $query->whereRaw('DATE(tgl_kembali_aktual) > DATE(tgl_kembali)');
                } elseif ($statusFilter === 'early') {
                    $query->whereRaw('DATE(tgl_kembali_aktual) < DATE(tgl_kembali)');
                }
            }
            
        } else {
            // Customer sees only their non-soft-deleted transactions
            $query = Transaksi::with(['user', 'mobil'])
                            ->where('user_id', $user->id);
            
            // Customer search (their own transactions)
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('total', 'LIKE', "%{$search}%")
                    ->orWhereHas('mobil', function($mq) use ($search) {
                        $mq->where('merek', 'LIKE', "%{$search}%")
                            ->orWhere('nopolisi', 'LIKE', "%{$search}%")
                            ->orWhere('jenis', 'LIKE', "%{$search}%");
                    });
                });
            }
            
            // Customer status options
            $statusOptions = [
                'all' => 'Semua Status',
                'Wait' => 'Menunggu',
                'Proses' => 'Diproses',
                'Selesai' => 'Selesai',
                'Terlambat' => 'Terlambat'
            ];
            
            // Apply status filter for customer
            if ($statusFilter && $statusFilter !== 'all') {
                $query->where('status', $statusFilter);
            }
        }
        
        // Date filter (available for both admin and customer)
        if ($dateFilter) {
            switch ($dateFilter) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year);
                    break;
                case 'year':
                    $query->whereYear('created_at', now()->year);
                    break;
            }
        }
        
        $transaksis = $query->latest()->paginate($perPage);
        
        // Date filter options
        $dateOptions = [
            'all' => 'Semua Waktu',
            'today' => 'Hari Ini',
            'week' => 'Minggu Ini',
            'month' => 'Bulan Ini',
            'year' => 'Tahun Ini'
        ];
        
        return view('transaksis.history', compact('transaksis', 'statusOptions', 'dateOptions'));
    }

    public function cetakPdf(Request $request)
    {
        $user = Auth::user();

        // Logika sama seperti history() untuk ambil data transaksi
        $search = $request->get('search');
        $statusFilter = $request->get('status_filter');
        $dateFilter = $request->get('date_filter');

        if ($user->role === 'admin') {
            $this->updateLateTransactions();

            $query = Transaksi::withTrashed()
                            ->with(['user', 'mobil'])
                            ->where('status', Transaksi::STATUS_SELESAI);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'LIKE', "%{$search}%")
                    ->orWhere('ponsel', 'LIKE', "%{$search}%")
                    ->orWhere('alamat', 'LIKE', "%{$search}%")
                    ->orWhere('total', 'LIKE', "%{$search}%")
                    ->orWhereHas('mobil', function ($mq) use ($search) {
                        $mq->where('merek', 'LIKE', "%{$search}%")
                            ->orWhere('nopolisi', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
                    });
                });
            }

            if ($statusFilter && $statusFilter !== 'all') {
                $query->whereNotNull('tgl_kembali_aktual');
                if ($statusFilter === 'ontime') {
                    $query->whereRaw('DATE(tgl_kembali_aktual) = DATE(tgl_kembali)');
                } elseif ($statusFilter === 'late') {
                    $query->whereRaw('DATE(tgl_kembali_aktual) > DATE(tgl_kembali)');
                } elseif ($statusFilter === 'early') {
                    $query->whereRaw('DATE(tgl_kembali_aktual) < DATE(tgl_kembali)');
                }
            }

        } else {
            $query = Transaksi::with(['user', 'mobil'])->where('user_id', $user->id);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('total', 'LIKE', "%{$search}%")
                    ->orWhereHas('mobil', function ($mq) use ($search) {
                        $mq->where('merek', 'LIKE', "%{$search}%")
                            ->orWhere('nopolisi', 'LIKE', "%{$search}%");
                    });
                });
            }

            if ($statusFilter && $statusFilter !== 'all') {
                $query->where('status', $statusFilter);
            }
        }

        if ($dateFilter) {
            switch ($dateFilter) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year);
                    break;
                case 'year':
                    $query->whereYear('created_at', now()->year);
                    break;
            }
        }

        $transaksis = $query->latest()->get();

        $pdf = PDF::loadView('transaksis.pdf', compact('transaksis', 'user'))->setPaper('a4', 'landscape');
        return $pdf->stream('laporan-transaksi.pdf');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($mobil_id = null)
    {
        // Jika tidak ada mobil_id dari route parameter, coba ambil dari query
        if (!$mobil_id) {
            $mobil_id = request()->query('mobil_id');
        }
        
        if (!$mobil_id) {
            return redirect()->route('transaksis.index')
                           ->with('error', 'Pilih mobil terlebih dahulu.');
        }
        
        $mobil = Mobil::findOrFail($mobil_id);
        $user = Auth::user();
        return view('transaksis.create', compact('mobil', 'user'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'mobil_id' => 'required|exists:mobils,id',
            'alamat' => 'required|string',
            'lama' => 'required|integer|min:1',
            'tgl_pesan' => 'required|date|after_or_equal:today',
        ]);

        // Get mobil to calculate total
        $mobil = Mobil::findOrFail($request->mobil_id);
        
        // Ensure lama is integer
        $lama = (int) $request->input('lama');
        $total = $mobil->harga * $lama;
        $user = Auth::user();

        // Calculate return date safely
        $tglPesan = Carbon::parse($request->input('tgl_pesan'));
        $tglKembali = $tglPesan->copy()->addDays($lama);

        $transaksiData = [
            'user_id' => $user->id,
            'mobil_id' => $request->input('mobil_id'),
            'nama' => $user->name,
            'ponsel' => $user->nohp ?? $user->email,
            'alamat' => $request->input('alamat'),
            'lama' => $lama, // Ensure integer
            'tgl_pesan' => $request->input('tgl_pesan'),
            'tgl_kembali' => $tglKembali,
            'total' => $total,
            'status' => Transaksi::STATUS_WAIT,
        ];

        Transaksi::create($transaksiData);

        return redirect()->route('transaksis.history')
                        ->with('success', 'Transaksi berhasil dibuat. Menunggu konfirmasi admin.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaksi $transaksi)
    {
        $transaksi->load(['user', 'mobil']);
        return view('transaksis.show', compact('transaksi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    /**
 * Show the form for editing the specified resource.
 */
    public function edit(Transaksi $transaksi)
    {
        $user = Auth::user();
        
        // Check if customer can edit (only if status is Wait)
        if ($user->role !== 'admin' && !$transaksi->canCustomerEdit()) {
            return redirect()->route('transaksis.history')
                        ->with('error', 'Transaksi hanya dapat diedit ketika status masih Wait.');
        }

        // Check if user owns this transaction (for customers)
        if ($user->role !== 'admin' && $transaksi->user_id !== $user->id) {
            return redirect()->route('transaksis.history')
                        ->with('error', 'Anda tidak memiliki akses untuk mengedit transaksi ini.');
        }

        // Load the related mobil data
        $transaksi->load(['mobil', 'user']);
        $mobils = Mobil::all();
        
        return view('transaksis.edit', compact('transaksi', 'mobils'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaksi $transaksi)
    {
        $user = Auth::user();
        
        // Check permissions for customers
        if ($user->role !== 'admin') {
            if (!$transaksi->canCustomerEdit()) {
                return redirect()->route('transaksis.history')
                            ->with('error', 'Transaksi hanya dapat diedit ketika status masih Wait.');
            }
            
            if ($transaksi->user_id !== $user->id) {
                return redirect()->route('transaksis.history')
                            ->with('error', 'Anda tidak memiliki akses untuk mengedit transaksi ini.');
            }
        }

        // Base validation rules
        $validationRules = [
            'mobil_id' => 'required|exists:mobils,id',
            'alamat' => 'required|string|max:500',
            'lama' => 'required|integer|min:1|max:30',
            'tgl_pesan' => 'required|date|after_or_equal:today',
        ];

        // Admin can update additional fields
        if ($user->role === 'admin') {
            $validationRules['nama'] = 'required|string|max:255';
            $validationRules['ponsel'] = 'required|string|max:50';
            
            // Get available status options for this transaction
            $availableStatuses = array_keys($transaksi->getAvailableStatusOptions());
            $validationRules['status'] = 'required|string|in:' . implode(',', $availableStatuses);
        }

        // Validate the request
        $request->validate($validationRules);

        // Get mobil to calculate total
        $mobil = Mobil::findOrFail($request->mobil_id);
        
        // Ensure lama is integer and calculate total
        $lama = (int) $request->input('lama');
        $total = $mobil->harga * $lama;

        // Calculate return date safely
        $tglPesan = Carbon::parse($request->input('tgl_pesan'));
        $tglKembali = $tglPesan->copy()->addDays($lama);

        // Prepare update data
        $transaksiData = [
            'mobil_id' => $request->input('mobil_id'),
            'alamat' => $request->input('alamat'),
            'lama' => $lama,
            'tgl_pesan' => $request->input('tgl_pesan'),
            'tgl_kembali' => $tglKembali->format('Y-m-d'),
            'total' => $total,
        ];

        // Admin can update additional fields
        if ($user->role === 'admin') {
            $transaksiData['nama'] = $request->input('nama');
            $transaksiData['ponsel'] = $request->input('ponsel');
            $transaksiData['status'] = $request->input('status');
            
            // Auto-fill tgl_kembali_aktual when status becomes "Selesai"
            if ($request->input('status') === Transaksi::STATUS_SELESAI && !$transaksi->tgl_kembali_aktual) {
                $transaksiData['tgl_kembali_aktual'] = Carbon::now()->format('Y-m-d');
            }
        }

        // Update the transaction
        $transaksi->update($transaksiData);

        // Redirect based on user role
        $redirectRoute = $user->role === 'admin' ? 'transaksis.admin-index' : 'transaksis.history';
        
        return redirect()->route($redirectRoute)
                        ->with('success', 'Transaksi berhasil diupdate.');
    }

        /**
         * Remove the specified resource from storage.
         */
        public function destroy($id)
    {
        $user = Auth::user();
        
        // Admin dapat menghapus semua transaksi (termasuk yang soft deleted)
        if ($user->role === 'admin') {
            $transaksi = Transaksi::withTrashed()->findOrFail($id);
            
            // Jika sudah soft deleted, lakukan force delete
            if ($transaksi->trashed()) {
                $transaksi->forceDelete();
                $message = 'Transaksi berhasil dihapus secara permanen.';
            } else {
                // Jika belum soft deleted, lakukan force delete langsung
                $transaksi->forceDelete();
                $message = 'Transaksi berhasil dihapus.';
            }
        } else {
            // Customer hanya bisa menghapus transaksi miliknya yang tidak soft deleted
            $transaksi = Transaksi::findOrFail($id);
            
            // Check if customer can delete (only if status is Selesai)
            if (!$transaksi->canCustomerDelete()) {
                return redirect()->route('transaksis.history')
                            ->with('error', 'Transaksi hanya dapat dihapus ketika status Selesai.');
            }
            
            // Check ownership
            if ($transaksi->user_id !== $user->id) {
                return redirect()->route('transaksis.history')
                            ->with('error', 'Anda tidak memiliki akses untuk menghapus transaksi ini.');
            }
            
            // Customer deletion = soft delete
            $transaksi->delete();
            $message = 'Transaksi berhasil dihapus.';
        }

        return redirect()->route('transaksis.history')
                        ->with('success', $message);
    }

    /**
     * Update status transaksi (untuk admin)
     */
    public function updateStatus(Request $request, Transaksi $transaksi)
    {
        // Only admin can update status
        if (Auth::user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Tidak memiliki akses.');
        }

        $availableStatuses = array_keys($transaksi->getAvailableStatusOptions());
        
        $request->validate([
            'status' => 'required|string|in:' . implode(',', $availableStatuses),
        ]);

        $updateData = ['status' => $request->status];
        
        // Auto-fill tgl_kembali_aktual when status becomes "Selesai"
        if ($request->status === Transaksi::STATUS_SELESAI && !$transaksi->tgl_kembali_aktual) {
            $updateData['tgl_kembali_aktual'] = Carbon::now()->format('Y-m-d');
        }

        $transaksi->update($updateData);

        return redirect()->back()->with('success', 'Status transaksi berhasil diupdate.');
    }

    /**
     * Update late transactions automatically
     */
    private function updateLateTransactions()
    {
        try {
            // Update hanya untuk transaksi yang belum selesai dan sudah terlambat
            $lateTransactions = Transaksi::where('status', '!=', Transaksi::STATUS_SELESAI)
                                         ->where('status', '!=', Transaksi::STATUS_TERLAMBAT)
                                         ->where('tgl_kembali', '<', Carbon::now()->format('Y-m-d'))
                                         ->get();
            
            foreach ($lateTransactions as $transaction) {
                $transaction->update([
                    'status' => Transaksi::STATUS_TERLAMBAT
                ]);
            }
        } catch (\Exception $e) {
            // Log error but don't break the application
            Log::error('Error updating late transactions: ' . $e->getMessage());
        }
    }
}