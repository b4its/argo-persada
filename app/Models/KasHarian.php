<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
        // Cegah transaksi/baris yang membuat saldo terkini menjadi negatif (sebelum simpan).
        static::saving(fn (KasHarian $kas) => static::assertSaldoNotMinus($kas));

        // Setiap kali data dibuat, diupdate, atau dihapus, hitung ulang saldo.
        static::saved(fn (KasHarian $kas) => static::recalculateBalances($kas->akun_keuangan_id));
        static::deleted(fn (KasHarian $kas) => static::recalculateBalances($kas->akun_keuangan_id));
    }

    /**
     * Pastikan kredit (uang keluar) tidak melebihi saldo yang tersedia,
     * sehingga saldo terkini tidak pernah negatif.
     */
    protected static function assertSaldoNotMinus(KasHarian $kas): void
    {
        $saldoAwal = (float) $kas->saldo_awal;
        $debet = (float) $kas->debet;
        $kredit = (float) $kas->kredit;

        if (($saldoAwal + $debet - $kredit) < 0) {
            throw ValidationException::withMessages([
                'kredit' => "Kredit (Rp " . number_format($kredit, 0, ',', '.') . ") melebihi saldo yang tersedia (Rp " . number_format($saldoAwal + $debet, 0, ',', '.') . "). Saldo tidak boleh minus.",
            ]);
        }
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

                // Saldo tidak boleh minus: jika ada, batalkan seluruh rekalkulasi (rollback).
                if ($saldoAkhir < 0) {
                    throw ValidationException::withMessages([
                        'kredit' => 'Saldo terkini tidak boleh minus. Periksa kembali transaksi pada akun ini.',
                    ]);
                }

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