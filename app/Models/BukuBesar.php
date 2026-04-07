<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'id_pesanan',
    'code',
    'name',
    'type',
    'periode'
])]
class BukuBesar extends Model
{
    protected $table = 'buku_besar';

    /**
     * Relasi ke tabel mutasi.
     * Satu Buku Besar dapat memiliki banyak mutasi.
     */
    public function mutasis(): HasMany
    {
        return $this->hasMany(Mutasi::class, 'id_buku_Besar');
    }

    public function pesanans(): HasMany
    {
        return $this->hasMany(Mutasi::class, 'id_pesanan');
    }
}