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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
