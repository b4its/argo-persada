<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'id_buku_Besar',
    'code',
    'name',
    'saldo_awal',
    'saldo_akhir',
])]
class Mutasi extends Model
{
    protected $table = 'mutasi';

    /**
     * Relasi ke tabel buku_besar.
     * Setiap mutasi dimiliki oleh satu Buku Besar.
     */
    public function bukuBesar(): BelongsTo
    {
        return $this->belongsTo(BukuBesar::class, 'id_buku_Besar');
    }

    /**
     * Relasi ke tabel mutasi_item.
     * Satu mutasi dapat memiliki banyak item mutasi.
     */
    public function mutasiItems(): HasMany
    {
        return $this->hasMany(MutasiItem::class, 'id_mutasi');
    }
}