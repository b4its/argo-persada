<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'queue_keranjang_id', 'sub_total'])]
class Keranjang extends Model
{
    protected $table = 'keranjang';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function queueKeranjang(): BelongsTo
    {
        return $this->belongsTo(QueueKeranjang::class, 'queue_keranjang_id');
    }

    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class);
    }
}