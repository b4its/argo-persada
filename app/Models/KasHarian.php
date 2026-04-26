<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KasHarian extends Model
{
    // Pastikan di tabel database memang menggunakan nama ini
    protected $table = 'kas_harian';

    protected $fillable = [
        'company_internal_id',
        'user_id',
        'pesanan_id',
        'akun_keuangan_id',
        'kategori',
        'toko',
        'saldo_awal',
        'debet',
        'kredit',
        'saldo_akhir',
        'keterangan'
    ];

    /**
     * Boot function untuk menangani event model secara otomatis.
     * Ini memastikan "Check and Balance" berjalan tanpa harus dipanggil manual.
     */
    protected static function booted(): void
    {
        // Setiap kali data dibuat, diupdate, atau dihapus, hitung ulang saldo.
        static::saved(fn (KasHarian $kas) => static::recalculateBalances($kas->akun_keuangan_id));
        static::deleted(fn (KasHarian $kas) => static::recalculateBalances($kas->akun_keuangan_id));
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function companyInternal(): BelongsTo
    {
        return $this->belongsTo(CompanyInternal::class, 'company_internal_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function akunKeuangan(): BelongsTo
    {
        return $this->belongsTo(AkunKeuangan::class, 'akun_keuangan_id');
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Logic: Automated Running Balance
    |--------------------------------------------------------------------------
    */

    public static function recalculateBalances($akunId): void
    {
        if (!$akunId) return;

        // Bungkus dalam transaction agar aman jika terjadi error di tengah jalan
        \Illuminate\Support\Facades\DB::transaction(function () use ($akunId) {
            
            $transactions = \Illuminate\Support\Facades\DB::table('kas_harian')
                ->where('akun_keuangan_id', $akunId)
                ->orderBy('created_at', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            $runningBalance = 0;

            foreach ($transactions as $transaction) {
                $saldoAwal = $runningBalance;
                $saldoAkhir = $saldoAwal + (float)$transaction->debet - (float)$transaction->kredit;

                // HANYA LAKUKAN UPDATE JIKA ADA PERUBAHAN SALDO
                // Ini menghemat pemakaian resource database secara masif
                if ($transaction->saldo_awal != $saldoAwal || $transaction->saldo_akhir != $saldoAkhir) {
                    \Illuminate\Support\Facades\DB::table('kas_harian')
                        ->where('id', $transaction->id)
                        ->update([
                            'saldo_awal' => $saldoAwal,
                            'saldo_akhir' => $saldoAkhir
                        ]);
                }

                $runningBalance = $saldoAkhir;
            }
        });
    }
}