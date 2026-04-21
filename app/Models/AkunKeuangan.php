<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AkunKeuangan extends Model
{
    //
    protected $table = 'akun_keuangan';

    protected $fillable = [
        'name',
        'kode',
        'kategori',
    ];

    /**
     * Relasi ke tabel mutasi.
     * Satu Buku Besar dapat memiliki banyak mutasi.
     */

    public function pesanans(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'id_pesanan');
    }

    public function kasHarian(): HasMany
    {
        return $this->hasMany(KasHarian::class, 'akun_keuangan_id');
    }
}
