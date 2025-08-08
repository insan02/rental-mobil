<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Transaksi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transaksis';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id',
        'mobil_id',
        'nama',
        'ponsel',
        'alamat',
        'lama',
        'tgl_pesan',
        'tgl_kembali',
        'tgl_kembali_aktual',
        'total',
        'status'
    ];

    protected $casts = [
        'lama' => 'integer',
        'total' => 'decimal:2',
        'tgl_pesan' => 'datetime',
        'tgl_kembali' => 'datetime',
        'tgl_kembali_aktual' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    // Status constants
    const STATUS_WAIT = 'Wait';
    const STATUS_PROSES = 'Proses';
    const STATUS_SELESAI = 'Selesai';
    const STATUS_TERLAMBAT = 'Terlambat';

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mobil()
    {
        return $this->belongsTo(Mobil::class);
    }

    // Accessor for tgl_kembali - FIXED VERSION
    public function getTglKembaliAttribute($value)
    {
        if (!$value && $this->tgl_pesan && $this->lama) {
            $lamaDays = is_numeric($this->lama) ? (int) $this->lama : 0;
            if ($lamaDays > 0) {
                return Carbon::parse($this->tgl_pesan)->addDays($lamaDays);
            }
        }
        return $value ? Carbon::parse($value) : null;
    }


    // Check if transaction is late
    public function isLate()
    {
        if ($this->status === self::STATUS_SELESAI || $this->status === self::STATUS_TERLAMBAT) {
            return false;
        }

        $expectedReturnDate = $this->tgl_kembali;
        return $expectedReturnDate instanceof Carbon && Carbon::now()->gt($expectedReturnDate);
    }


    // Check if actual return was late
    public function wasReturnedLate()
    {
        if (!$this->tgl_kembali_aktual || !$this->tgl_kembali) {
            return false;
        }

        $actualReturn = Carbon::parse($this->tgl_kembali_aktual);
        $expectedReturn = Carbon::parse($this->tgl_kembali);
        
        return $actualReturn->gt($expectedReturn);
    }


    // Get days difference between expected and actual return
    public function getDaysLateDifference()
    {
        if (!$this->tgl_kembali_aktual || !$this->tgl_kembali) {
            return 0;
        }

        $actualReturn = Carbon::parse($this->tgl_kembali_aktual);
        $expectedReturn = Carbon::parse($this->tgl_kembali);
        
        return $expectedReturn->diffInDays($actualReturn, false);
    }


    // Get available status options based on current status
    public function getAvailableStatusOptions()
    {
        $allStatuses = [
            self::STATUS_WAIT => 'Wait',
            self::STATUS_PROSES => 'Proses', 
            self::STATUS_SELESAI => 'Selesai',
            self::STATUS_TERLAMBAT => 'Terlambat'
        ];

        switch ($this->status) {
            case self::STATUS_WAIT:
                return [
                    self::STATUS_WAIT => 'Wait',
                    self::STATUS_PROSES => 'Proses'
                ];
            case self::STATUS_PROSES:
                return [
                    self::STATUS_PROSES => 'Proses',
                    self::STATUS_SELESAI => 'Selesai'
                ];
            case self::STATUS_SELESAI:
            case self::STATUS_TERLAMBAT:
                return [
                    $this->status => $allStatuses[$this->status]
                ];
            default:
                return $allStatuses;
        }
    }

    // Check if customer can edit
    public function canCustomerEdit()
    {
        return $this->status === self::STATUS_WAIT;
    }

    // Check if customer can delete
    public function canCustomerDelete()
    {
        return $this->status === self::STATUS_SELESAI;
    }

    // Boot method to handle automatic status updates
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($transaksi) {
            // Auto-calculate tgl_kembali if not set
            if (!$transaksi->tgl_kembali && $transaksi->tgl_pesan && $transaksi->lama) {
                $lamaDays = is_numeric($transaksi->lama) ? (int) $transaksi->lama : 0;
                if ($lamaDays > 0) {
                    $transaksi->tgl_kembali = Carbon::parse($transaksi->tgl_pesan)->addDays($lamaDays);
                }
            }

            // Auto-update to Terlambat if late and not completed
            if ($transaksi->isLate() && $transaksi->status !== self::STATUS_SELESAI) {
                $transaksi->status = self::STATUS_TERLAMBAT;
            }
        });
    }

    // Scope for checking late transactions
    public function scopeLateTransactions($query)
{
    return $query->whereNotIn('status', [self::STATUS_SELESAI, self::STATUS_TERLAMBAT])
                 ->whereRaw('DATE_ADD(tgl_pesan, INTERVAL lama DAY) < NOW()');
}

}