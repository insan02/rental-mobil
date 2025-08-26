<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mobil extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mobils';
    protected $primaryKey = 'id';
    protected $fillable = ['user_id', 'nopolisi', 'merek', 'jenis', 'kapasitas', 'harga', 'foto'];

    // Relationship dengan User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // PERBAIKAN: Hapus relationship mobil() yang salah
    // Relationship ini tidak masuk akal karena Mobil tidak berelasi dengan Mobil lain
    // Kemungkinan ini adalah copy-paste error dari model lain

    // TAMBAHAN: Relationship dengan Transaksi
    /**
     * Get all transactions for this mobil
     */
    public function transaksis(): HasMany
    {
        return $this->hasMany(Transaksi::class);
    }

    /**
     * Get active transactions (Wait or Proses status)
     */
    public function activeTransactions(): HasMany
    {
        return $this->hasMany(Transaksi::class)
                    ->whereIn('status', [
                        \App\Models\Transaksi::STATUS_WAIT, 
                        \App\Models\Transaksi::STATUS_PROSES
                    ]);
    }

    /**
     * Get completed transactions
     */
    public function completedTransactions(): HasMany
    {
        return $this->hasMany(Transaksi::class)
                    ->where('status', \App\Models\Transaksi::STATUS_SELESAI);
    }

    /**
     * Check if mobil has active transactions
     */
    public function hasActiveTransactions(): bool
    {
        return $this->activeTransactions()->exists();
    }

    /**
     * Get count of active transactions
     */
    public function getActiveTransactionsCount(): int
    {
        return $this->activeTransactions()->count();
    }

    /**
     * Get latest transaction
     */
    public function latestTransaction()
    {
        return $this->hasOne(Transaksi::class)->latestOfMany();
    }

    /**
     * Check if mobil is currently available for rent
     */
    public function isAvailable(): bool
    {
        return !$this->hasActiveTransactions();
    }

    /**
     * Get mobil availability status
     */
    public function getAvailabilityStatus(): string
    {
        if ($this->hasActiveTransactions()) {
            $activeCount = $this->getActiveTransactionsCount();
            return "Sedang disewa ($activeCount transaksi aktif)";
        }
        return "Tersedia";
    }

    /**
     * Get mobil availability badge class for UI
     */
    public function getAvailabilityBadgeClass(): string
    {
        return $this->isAvailable() ? 'bg-success' : 'bg-warning';
    }

    // HAPUS: Semua accessor method yang tidak relevan
    // Method-method ini sepertinya copy-paste dari model Transaksi
    // dan tidak relevan untuk model Mobil
    
    /**
     * Scope for available mobils (not in active transactions)
     */
    public function scopeAvailable($query)
    {
        return $query->whereDoesntHave('transaksis', function ($q) {
            $q->whereIn('status', [
                \App\Models\Transaksi::STATUS_WAIT,
                \App\Models\Transaksi::STATUS_PROSES
            ]);
        });
    }

    /**
     * Scope for mobils currently in use
     */
    public function scopeInUse($query)
    {
        return $query->whereHas('transaksis', function ($q) {
            $q->whereIn('status', [
                \App\Models\Transaksi::STATUS_WAIT,
                \App\Models\Transaksi::STATUS_PROSES
            ]);
        });
    }

    /**
     * Scope for search functionality
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nopolisi', 'like', '%' . $search . '%')
              ->orWhere('merek', 'like', '%' . $search . '%')
              ->orWhere('jenis', 'like', '%' . $search . '%')
              ->orWhere('kapasitas', 'like', '%' . $search . '%');
        });
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }

    /**
     * Get jenis badge class for UI
     */
    public function getJenisBadgeClass(): string
    {
        return match($this->jenis) {
            'Sedan' => 'bg-info',
            'MPV' => 'bg-warning',
            'SUV' => 'bg-success',
            default => 'bg-secondary'
        };
    }
}

