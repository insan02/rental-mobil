<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $nohp
 * @property string $password
 * @property string $role
 * @property string|null $foto
 * @property \Carbon\Carbon|null $email_verified_at
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'email', 'nohp', 'password', 'role', 'foto'];
    protected $hidden = ['password', 'remember_token'];
    
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }
    
    // Relationship dengan transaksi
    public function transaksis()
    {
        return $this->hasMany(Transaksi::class);
    }
    
    // Method untuk cek apakah user memiliki transaksi aktif
    public function hasActiveTransactions()
    {
        return $this->transaksis()
                   ->whereIn('status', ['Wait', 'Proses', 'Terlambat'])
                   ->exists();
    }
    
    // Method untuk mendapatkan jumlah transaksi aktif
    public function getActiveTransactionsCount()
    {
        return $this->transaksis()
                   ->whereIn('status', ['Wait', 'Proses', 'Terlambat'])
                   ->count();
    }
    
    // Method untuk mendapatkan detail transaksi aktif
    public function getActiveTransactions()
    {
        return $this->transaksis()
                   ->whereIn('status', ['Wait', 'Proses', 'Terlambat'])
                   ->with('mobil')
                   ->get();
    }
}