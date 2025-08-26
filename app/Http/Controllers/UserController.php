<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10); // Default 10 items per page
        $search = $request->get('search');
        
        $query = User::where('role', 'customer')
                    ->withCount(['transaksis as active_transactions_count' => function($q) {
                        $q->whereIn('status', ['Wait', 'Proses', 'Terlambat']);
                    }]);
        
        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%")
                ->orWhere('nohp', 'LIKE', "%{$search}%");
            });
        }
        
        $users = $query->latest()->paginate($perPage);
        
        return view('users.index', compact('users'));
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            // Cek apakah user memiliki transaksi aktif
            if ($user->hasActiveTransactions()) {
                $activeTransactions = $user->getActiveTransactions();
                $activeCount = $user->getActiveTransactionsCount();
                
                // Buat pesan detail tentang transaksi aktif
                $transactionDetails = $activeTransactions->map(function($transaction) {
                    return sprintf(
                        "• %s (%s) - Status: %s", 
                        $transaction->mobil->merek ?? 'Mobil tidak tersedia',
                        $transaction->created_at->format('d M Y'),
                        $transaction->status
                    );
                })->join('<br>');
                
                return redirect()->route('users.index')
                    ->with('error', 
                        "User <strong>{$user->name}</strong> tidak dapat dihapus karena memiliki {$activeCount} transaksi aktif:<br><br>" .
                        $transactionDetails . 
                        "<br><br>Silakan selesaikan atau batalkan transaksi tersebut terlebih dahulu."
                    );
            }
            
            // Get current authenticated user ID safely
            $currentUserId = Auth::id();
            
            // Only log if user is authenticated
            if ($currentUserId) {
                Log::info("Admin menghapus user", [
                    'admin_id' => $currentUserId,
                    'deleted_user_id' => $user->id,
                    'deleted_user_name' => $user->name,
                    'deleted_user_email' => $user->email,
                    'total_completed_transactions' => $user->transaksis()->where('status', 'Selesai')->count()
                ]);
            }
            
            // Soft delete user
            $userName = $user->name;
            $completedTransactions = $user->transaksis()->where('status', 'Selesai')->count();
            
            $user->delete();
            
            return redirect()->route('users.index')
                ->with('success', 
                    "User <strong>{$userName}</strong> berhasil dihapus. " .
                    "Data riwayat {$completedTransactions} transaksi selesai tetap tersimpan untuk keperluan laporan."
                );
                
        } catch (\Exception $e) {
            // Get current authenticated user ID safely
            $currentUserId = Auth::id();
            
            Log::error("Error saat menghapus user", [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'admin_id' => $currentUserId // This will be null if not authenticated
            ]);
            
            return redirect()->route('users.index')
                ->with('error', 'Terjadi kesalahan saat menghapus user. Silakan coba lagi.');
        }
    }
    
    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        // Load transaksi dengan status untuk detail user
        $user->load(['transaksis' => function($query) {
            $query->with('mobil')->latest();
        }]);
        
        return view('users.show', compact('user'));
    }
}