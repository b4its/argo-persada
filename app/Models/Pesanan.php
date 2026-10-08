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
        'tanggal_po',
        'code', 
        'tipe_pesanan',
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
        'metode_pembayaran_rilis_dana',
        'nama_bank_rilis_dana',
        'no_rekening_rilis_dana',
        'nama_bank_lunas',
        'no_rekening_lunas',
        'metode_pembayaran_lunas',
        'status_pesanan',
        'pesanan_status',
        'status_perilisan_dana',
        'file_invoice',
        'file_do',
        'keterangan_logistik'
    ];

    protected function casts(): array
    {
        return [
            'tanggal_po' => 'date',
            'tanggal_rilis_dana' => 'date',
            'tanggal_terbit_surat_jalan' => 'date',
            'tanggal_terbit_invoice' => 'date',
            'tanggal_jatuh_tempo' => 'date',
            'tanggal_surat_kembali' => 'date',
            'tanggal_lunas' => 'date',
            'validasi_tanggal_lunas' => 'date',
        ];
    }

    public function getEffectiveTanggalPoAttribute(): string
    {
        if ($this->tanggal_po) {
            return $this->tanggal_po instanceof \Carbon\Carbon ? $this->tanggal_po->format('d-m-Y') : \Carbon\Carbon::parse($this->tanggal_po)->format('d-m-Y');
        }
        return $this->created_at ? $this->created_at->format('d-m-Y') : date('d-m-Y');
    }

    public function getTanggalPemesananAttribute()
    {
        return $this->tanggal_po ?? $this->created_at;
    }

    public function generateCombinationInvoiceNumber(): string
    {
        $prefix = 'INV';

        // Kode Perusahaan / Divisi
        $companyCode = $this->companyInternal?->singkatan 
            ?: ($this->company_internal_id ? CompanyInternal::find($this->company_internal_id)?->singkatan : null)
            ?: ($this->tipe_pesanan == 1 ? 'PRJ' : 'AAP');

        $divisi = 'FIN';

        $bulanRomawi = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
        ];
        $month = (int) date('n');
        $monthRoman = $bulanRomawi[$month] ?? date('m');
        $year = date('Y');

        $seq = sprintf('%04d', $this->id ?: (static::max('id') + 1));

        return "{$prefix}/{$companyCode}-{$divisi}/{$monthRoman}/{$year}/{$seq}";
    }

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
        return $this->hasMany(BukuBesar::class, 'id_pesanan');
    }
    
}