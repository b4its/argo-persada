<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'supplier_name', 'keterangan', 'item_name', 'quantity', 'satuan', 'modal', 'po', 'sub_total'])]
class QueueKeranjang extends Model
{
    protected $table = 'queue_keranjang';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function keranjang(): HasMany
    {
        return $this->hasMany(Keranjang::class, 'queue_keranjang_id');
    }
}