<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'keranjang_id', 'code', 'group_name', 'company_name', 'address'])]
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
}