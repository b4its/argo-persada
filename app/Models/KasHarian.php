<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class KasHarian extends Model
{
    //
    protected $table = 'kas_harian';


    protected $fillable = [
        'saldo_id',
        'company_internal_id',
        'user_id',
        'pesanan_id',
        'debet',
        'kredit',
        'keterangan'
    ];

    public function saldo(): BelongsTo
    {
        return $this->BelongsTo(Saldo::class, 'saldo_id');
    }

    public function companyInternal(): BelongsTo
    {
        return $this->belongsTo(CompanyInternal::class, 'company_internal_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }
}


            // $table->foreignId('saldo_id')->nullable()->constrained('saldo')->onDelete('cascade');
            // $table->foreignId('pesanan_id')->nullable()->constrained('pesanan_id')->onDelete('cascade');
            // $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // $table->foreignId('pesanan_id')->nullable()->constrained('pesanan')->onDelete('cascade');
            // $table->string('keterangan')->nullable();