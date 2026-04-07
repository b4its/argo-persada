<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'id_mutasi',
    'no_ref',
    'keterangan',
    'debet',
    'kredit',
    'saldo',
])]
class MutasiItem extends Model
{
    protected $table = 'mutasi_item';

    /**
     * Relasi ke tabel mutasi.
     * Setiap item mutasi dimiliki oleh satu mutasi utama.
     */
    public function mutasi(): BelongsTo
    {
        return $this->belongsTo(Mutasi::class, 'id_mutasi');
    }
}