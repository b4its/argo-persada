<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KasHarian extends Model
{
    // Nama tabel sudah benar sesuai standar plural Laravel, 
    // tapi pastikan di database memang 'kas_harian' (singular) atau 'kas_harians' (plural default).
    protected $table = 'kas_harian';

    protected $fillable = [
        'company_internal_id',
        'user_id',
        'pesanan_id',
        'akun_keuangan_id',
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
    protected static function booted()
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
    | Logic: Check and Balance
    |--------------------------------------------------------------------------
    */

    public static function recalculateBalances($akunId)
        {
            if (!$akunId) return;

            $transactions = self::where('akun_keuangan_id', $akunId)
                ->orderBy('created_at', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            // Kita tidak lagi menggunakan $runningBalance untuk menimpa saldo_awal baris berikutnya
            foreach ($transactions as $transaction) {
                if (!$transaction instanceof \Illuminate\Database\Eloquent\Model) {
                    continue; 
                }

                /**
                 * Saldo akhir adalah hasil kalkulasi dari komponen di baris itu sendiri.
                 * Ini memberikan fleksibilitas jika Anda ingin mengubah saldo_awal secara manual
                 * melalui form Filament tanpa ditimpa oleh saldo transaksi sebelumnya.
                 */
                $transaction->saldo_akhir = (float)$transaction->saldo_awal + (float)$transaction->debet - (float)$transaction->kredit;
                
                $transaction->timestamps = false;
                $transaction->saveQuietly();
            }
        }
}