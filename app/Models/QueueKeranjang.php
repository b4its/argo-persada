<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'keranjang_id', 'kode', 'supplier_name', 'keterangan', 'item_name', 'quantity', 'satuan', 'modal', 'po', 'sub_total'])]
class QueueKeranjang extends Model
{
    protected $table = 'queue_keranjang';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Tiap QueueKeranjang merujuk ke 1 Keranjang
    public function keranjang(): BelongsTo
    {
        return $this->belongsTo(Keranjang::class, 'keranjang_id');
    }
}