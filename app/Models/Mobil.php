<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mobil extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mobils';
    protected $primaryKey = 'id';
    protected $fillable = ['user_id', 'nopolisi', 'merek', 'jenis', 'kapasitas', 'harga', 'foto'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mobil()
    {
        return $this->belongsTo(Mobil::class)->withTrashed(); // Include soft deleted
    }

    // Alternative method untuk mendapatkan data mobil dengan fallback
    public function getMobilData()
    {
        // Coba ambil dari relationship dulu
        if ($this->relationLoaded('mobil') && $this->mobil) {
            return $this->mobil;
        }
        
        // Jika tidak ada, coba ambil langsung dari database (termasuk soft deleted)
        return Mobil::withTrashed()->find($this->mobil_id);
    }

    // Accessor untuk data mobil yang aman
    public function getMobilMerekAttribute()
    {
        $mobil = $this->getMobilData();
        return $mobil ? $mobil->merek : 'Mobil Tidak Tersedia';
    }

    public function getMobilNopolisiAttribute()
    {
        $mobil = $this->getMobilData();
        return $mobil ? $mobil->nopolisi : '-';
    }

    public function getMobilJenisAttribute()
    {
        $mobil = $this->getMobilData();
        return $mobil ? $mobil->jenis : '-';
    }

    public function getMobilKapasitasAttribute()
    {
        $mobil = $this->getMobilData();
        return $mobil ? $mobil->kapasitas : '-';
    }

    public function getMobilFotoAttribute()
    {
        $mobil = $this->getMobilData();
        return $mobil ? $mobil->foto : null;
    }
}

