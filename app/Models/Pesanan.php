<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id', 
    'keranjang_id', 
    'code', 
    'group_name', 
    'company_name', 
    'address',
    'ppn',
    'total_harga',
    'no_requisition',
    'no_invoice',
    'no_delivery_order',
    'tanggal_rilis_dana',
    'tanggal_terbit_invoice',
    'tanggal_jatuh_tempo',
    'tanggal_lunas',
    'file_invoice',
    'file_do'
])]
class Pesanan extends Model
{
    protected $table = 'pesanan';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function keranjang(): BelongsTo
    {
        return $this->belongsTo(Keranjang::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function queueDeletes(): HasMany
    {
        return $this->hasMany(QueueDelete::class);
    }
}