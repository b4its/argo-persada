<?php

namespace App\Filament\Resources\Marketing\MarketingPemesanans\Pages;

use App\Filament\Resources\Marketing\MarketingPemesanans\MarketingPemesananResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Str;
use App\Models\Pesanan;
use App\Models\Keranjang;
use App\Models\QueueKeranjang;
use Filament\Actions\CreateAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Filament\Schemas\Schema;

class ListMarketingPemesanans extends ListRecords
{
    protected static string $resource = MarketingPemesananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                // Memanggil Form Schema yang Anda buat (tidak perlu diubah)
                ->form(
                    \App\Filament\Resources\Marketing\MarketingPemesanans\Schemas\MarketingPemesananForm::configure(Schema::make())->getComponents()
                )
                // 1. Siapkan data hidden
                ->mutateFormDataUsing(function (array $data): array {
                    $data['user_id'] = auth()->id();
                    $data['code'] = 'PO-' . time();
                    return $data;
                })
                // 2. Ambil alih proses penyimpanan menggunakan transaksi DB
                ->using(function (array $data, string $model): Model {
                    return DB::transaction(function () use ($data) {
                        $totalKeseluruhan = 0;

                        // LANGKAH 1: Buat Keranjangnya dulu sebagai wadah
                        $keranjang = Keranjang::create([
                            'user_id' => $data['user_id'],
                            'sub_total' => 0, // Inisialisasi awal 0, nanti diupdate
                        ]);

                        // LANGKAH 2: Looping barang-barang di Repeater
                        if (!empty($data['list_barang'])) {
                            foreach ($data['list_barang'] as $barang) {
                                // Hitung sub_total per barang
                                $subTotalBarang = $barang['quantity'] * $barang['po'];
                                
                                // Akumulasi ke total harga keseluruhan keranjang
                                $totalKeseluruhan += $subTotalBarang;

                                // Buat data barang, dan KAITKAN langsung ke ID Keranjang yang dibuat di Langkah 1
                                QueueKeranjang::create([
                                    'user_id' => $data['user_id'],
                                    'keranjang_id' => $keranjang->id, // INI KUNCI RELASINYA
                                    'item_name' => $barang['item_name'],
                                    'quantity' => $barang['quantity'],
                                    'satuan' => $barang['satuan'],
                                    'modal' => $barang['modal'],
                                    'po' => $barang['po'],
                                    'supplier_name' => $barang['supplier_name'],
                                    'keterangan' => $barang['keterangan'] ?? null,
                                    'sub_total' => $subTotalBarang,
                                ]);
                            }
                        }

                        // LANGKAH 3: Setelah looping selesai, update total akhir di tabel Keranjang
                        $keranjang->update([
                            'sub_total' => $totalKeseluruhan
                        ]);

                        // LANGKAH 4: Buat data Pesanan utama, hubungkan ke Keranjang tersebut
                        $pesanan = Pesanan::create([
                            'user_id' => $data['user_id'],
                            'keranjang_id' => $keranjang->id,
                            'code' => $data['code'],
                            'group_name' => $data['group_name'],
                            'company_name' => $data['company_name'],
                            'address' => $data['address'],
                        ]);

                        // Filament butuh kembalian instance model agar tau proses berhasil
                        return $pesanan;
                    });
                }),
        ];
    }
}