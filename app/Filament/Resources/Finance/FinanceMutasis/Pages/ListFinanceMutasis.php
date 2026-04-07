<?php

namespace App\Filament\Resources\Finance\FinanceMutasis\Pages;

use App\Filament\Resources\Finance\FinanceMutasis\FinanceMutasiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Model; // Pastikan import ini ditambahkan

class ListFinanceMutasis extends ListRecords
{
    protected static string $resource = FinanceMutasiResource::class;
    protected static ?string $title = 'Daftar Mutasi Buku Besar';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label("Tambahkan Mutasi")
                ->mutateFormDataUsing(function (array $data): array {
                    $bulan = date('m');              // Format: 01-12
                    $tahun = date('y');              // Format: 26 (dua angka terakhir)
                    $tanggal = date('d');            // Format: 01-31
                    $random = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT); // 3 karakter acak
                    
                    $data['code'] = "{$bulan}{$tahun}-{$tanggal}-{$random}";
                    $data['type'] = "mutasi";
                    
                    return $data;
                })
                // LOGIKA KALKULASI BERADA DI SINI (Dijalankan setelah semua data tersimpan di DB)
                ->after(function (Model $record) {
                    // $record adalah instance BukuBesar yang baru saja dibuat
                    foreach ($record->mutasis as $mutasi) {
                        $saldoAwal = $mutasi->saldo_awal ?? 0;
                        $currentSaldo = $saldoAwal;

                        // Ambil semua item transaksi, urutkan berdasarkan ID (urutan input)
                        $items = $mutasi->mutasiItems()->orderBy('id', 'asc')->get();
                        
                        foreach ($items as $item) {
                            $debet = $item->debet ?? 0;
                            $kredit = $item->kredit ?? 0;
                            
                            // Kalkulasi saldo berjalan: Saldo saat ini + debet - kredit
                            $currentSaldo = $currentSaldo + $debet - $kredit;
                            
                            // Simpan nilai saldo ke tabel mutasi_item
                            $item->update(['saldo' => $currentSaldo]);
                        }

                        // Simpan nilai hasil akhir saldo ke saldo_akhir di tabel mutasi
                        $mutasi->update(['saldo_akhir' => $currentSaldo]);
                    }
                }),
        ];
    }
}