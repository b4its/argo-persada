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

    /**
     * Menghasilkan kode acak kombinasi huruf kapital dan angka (contoh: AC7X).
     */
    public static function generateRandomAlphaNumericCode(int $length = 4): string
    {
        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charsLength = strlen($chars);

        do {
            $code = '';
            for ($i = 0; $i < $length; $i++) {
                $code .= $chars[random_int(0, $charsLength - 1)];
            }
        } while (!preg_match('/[A-Z]/', $code) || !preg_match('/[0-9]/', $code));

        return $code;
    }

    /**
     * Menghasilkan nomor requisition unik dengan rumus tahun, bulan, hari + kode unik acak huruf & angka.
     * Contoh: 261008-AC7X
     */
    public static function generateRequisitionNumber(\DateTimeInterface|string|null $date = null, bool $withSeparator = true): string
    {
        if ($date instanceof \DateTimeInterface) {
            $datePart = $date->format('ymd');
        } elseif (!empty($date) && ($timestamp = strtotime((string) $date)) !== false) {
            $datePart = date('ymd', $timestamp);
        } else {
            $datePart = date('ymd');
        }

        do {
            $uniqueCode = static::generateRandomAlphaNumericCode(4);
            $noRequisition = $withSeparator ? "{$datePart}-{$uniqueCode}" : "{$datePart}{$uniqueCode}";
        } while (static::where('no_requisition', $noRequisition)->exists());

        return $noRequisition;
    }

    /**
     * Memperbaiki dan memperbarui record requisition number lama di database
     * agar memiliki format tanggal (ymd) + kode unik alfanumerik acak (contoh: 261008-AC7X).
     *
     * @return array{total_scanned: int, updated: int, skipped: int}
     */
    public static function fixExistingRequisitionNumbers(): array
    {
        $stats = [
            'total_scanned' => 0,
            'updated' => 0,
            'skipped' => 0,
        ];

        $records = static::whereNotNull('no_requisition')
            ->where('no_requisition', '!=', '')
            ->where('no_requisition', '!=', '---:---')
            ->get();

        $stats['total_scanned'] = $records->count();

        foreach ($records as $pesanan) {
            $oldReq = trim($pesanan->no_requisition);

            // Jika formatnya sudah sesuai standard unik baru: 6 digit tanggal + '-' + 4 karakter alfanumerik
            if (preg_match('/^\d{6}-[A-Z0-9]{4}$/', $oldReq)) {
                $stats['skipped']++;
                continue;
            }

            // Ekstrak atau tentukan bagian tanggal (ymd)
            if (preg_match('/^(\d{6})/', $oldReq, $matches)) {
                // Jika format lama diawali 6 digit tanggal (misal 261006 atau 261007)
                $datePart = $matches[1];
            } elseif (!empty($pesanan->tanggal_po) && ($ts = strtotime((string) $pesanan->tanggal_po)) !== false) {
                $datePart = date('ymd', $ts);
            } elseif (!empty($pesanan->created_at)) {
                $datePart = $pesanan->created_at->format('ymd');
            } else {
                $datePart = date('ymd');
            }

            do {
                $uniqueCode = static::generateRandomAlphaNumericCode(4);
                $newReq = "{$datePart}-{$uniqueCode}";
            } while (static::where('no_requisition', $newReq)->where('id', '!=', $pesanan->id)->exists());

            $pesanan->no_requisition = $newReq;
            $pesanan->saveQuietly();

            // Sinkronkan akun keuangan jika ada akun yang merujuk nomor requisition lama
            \Illuminate\Support\Facades\DB::table('akun_keuangan')
                ->where('name', 'Pesanan Barang No ' . $oldReq)
                ->update(['name' => 'Pesanan Barang No ' . $newReq]);

            // Sinkronkan task title jika ada yang merujuk nomor requisition lama
            \Illuminate\Support\Facades\DB::table('task')
                ->where('pesanan_id', $pesanan->id)
                ->where('title', 'like', "%dengan No Requisition {$oldReq}%")
                ->update([
                    'title' => \Illuminate\Support\Facades\DB::raw("REPLACE(title, 'dengan No Requisition {$oldReq}', 'dengan No Requisition {$newReq}')")
                ]);

            $stats['updated']++;
        }

        return $stats;
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