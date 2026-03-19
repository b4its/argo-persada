<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'sub_total'])]
class Keranjang extends Model
{
    protected $table = 'keranjang';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // 1 Keranjang memiliki BANYAK QueueKeranjang
    public function queueKeranjang(): HasMany
    {
        return $this->hasMany(QueueKeranjang::class, 'keranjang_id');
    }

    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class);
    }
}