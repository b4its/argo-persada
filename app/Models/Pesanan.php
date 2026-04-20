<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pesanan extends Model
{
    protected $table = 'pesanan';

    protected $fillable = [
        'user_id', 
        'keranjang_id', 
        'company_internal_id', 
        'saldo_id', 
        'code', 
        'group_name', 
        'company_name', 
        'address',
        'ppn',
        'total_harga',
        'no_po',
        'no_requisition',
        'no_invoice',
        'no_delivery_order',
        'tanggal_rilis_dana',
        'tanggal_terbit_invoice',
        'tanggal_jatuh_tempo',
        'tanggal_terbit_surat_jalan',
        'tanggal_surat_kembali',
        'tanggal_lunas',
        'validasi_tanggal_lunas',
        'pesanan_status',
        'status_perilisan_dana',
        'file_invoice',
        'file_do'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function keranjang(): BelongsTo
    {
        return $this->belongsTo(Keranjang::class);
    }
    public function companyInternal(): BelongsTo 
    {
        return $this->belongsTo(CompanyInternal::class, 'company_internal_id');
    }

    public function kasHarian(): HasMany 
    {
        return $this->hasMany(KasHarian::class, 'pesanan_id');
    }


    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function bukuBesar(): HasMany
    {
        return $this->hasMany(BukuBesar::class);
    }
    
}