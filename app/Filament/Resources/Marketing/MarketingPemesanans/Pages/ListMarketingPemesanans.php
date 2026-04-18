<?php

namespace App\Filament\Resources\Marketing\MarketingPemesanans\Pages;

use App\Filament\Resources\Marketing\MarketingPemesanans\MarketingPemesananResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Str;
use App\Models\Pesanan;
use App\Models\Keranjang;
use App\Models\QueueKeranjang;
use App\Models\LogActivities;
use App\Models\Task;
use App\Models\TaskActivity;
use Filament\Actions\CreateAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Filament\Schemas\Schema;

class ListMarketingPemesanans extends ListRecords
{
    protected static ?string $title = 'Daftar Pemesanan';
    protected static string $resource = MarketingPemesananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                // PERBAIKAN 1: Deklarasikan model secara eksplisit agar form tahu konteks datanya
                ->model(Pesanan::class) 
                ->form(
                    \App\Filament\Resources\Marketing\MarketingPemesanans\Schemas\MarketingPemesananForm::configure(Schema::make())->getComponents()
                )
                ->mutateFormDataUsing(function (array $data): array {
                    // PERBAIKAN 2: Pastikan data bawaan form tetap aman
                    $data['user_id'] = auth()->id();
                    $data['code'] = 'PO-' . time();
                    
                    return $data;
                })
                ->using(function (array $data, string $model): Model {
                    return DB::transaction(function () use ($data) {
                        $totalKeseluruhan = 0;

                        // LANGKAH 1: Buat Keranjang sebagai wadah
                        $keranjang = Keranjang::create([
                            'user_id' => $data['user_id'],
                            'sub_total' => 0,
                        ]);

                        // LANGKAH 2: Looping barang di Repeater
                        if (!empty($data['list_barang'])) {
                            foreach ($data['list_barang'] as $barang) {
                                $subTotalBarang = $barang['quantity'] * $barang['po'];
                                $totalKeseluruhan += $subTotalBarang;

                                QueueKeranjang::create([
                                    'user_id' => $data['user_id'],
                                    'keranjang_id' => $keranjang->id,
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

                        // LANGKAH 3: Update sub_total Keranjang
                        $keranjang->update([
                            'sub_total' => $totalKeseluruhan
                        ]);

                        $tax_amount = $totalKeseluruhan * 0.11;
                        $generate_po_number = 'PO-' . date('Ymd') . '-' . strtoupper(Str::random(5));
                        
                        // LANGKAH 4: Buat Pesanan utama
                        // PERBAIKAN 3: Gunakan null coalescing (?? null) agar tidak terjadi undefined key
                        $pesanan = Pesanan::create([
                            'user_id'             => $data['user_id'],
                            'keranjang_id'        => $keranjang->id,
                            'company_internal_id' => $data['company_internal_id'] ?? null, 
                            'saldo_id'        =>     $data['saldo_id'],
                            'no_po'               => $generate_po_number,
                            'code'                => $data['code'],
                            'ppn'                 => $tax_amount,
                            'total_harga'         => $totalKeseluruhan + $tax_amount,
                            'group_name'          => $data['group_name'] ?? null,
                            'company_name'        => $data['company_name'] ?? null,
                            'address'             => $data['address'] ?? null,
                        ]);

                        // LANGKAH 5: Buat Task
                        $task = Task::create([
                            'pesanan_id'  => $pesanan->id,
                            'title'       => 'Melakukan cetak surat requisition untuk pesanan ' . $pesanan->code,
                            'role'        => 'marketing',
                            'description' => 'Mohon untuk melakukan cetak surat requisition pada pesanan ' . $pesanan->code,
                            'due_date'    => now()->addDays(7),
                            'status'      => 0,
                        ]);

                        // LANGKAH 6: Buat Task Activity
                        TaskActivity::create([
                            'created_user_id' => $data['user_id'],
                            'updated_user_id' => $data['user_id'],
                            'task_id'         => $task->id,
                            'note'            => 'Pesanan baru berhasil dibuat dan akan diteruskan untuk melakukan cetak surat requisition.',
                            'pesanan_status'  => 0, 
                        ]);

                        // LANGKAH 7: Catat Log Activities
                        LogActivities::create([
                            'user_id'     => $data['user_id'],
                            'action'      => 'Create Pesanan',
                            'description' => 'Marketing membuat pesanan baru dengan kode ' . $pesanan->code,
                            'oldData'     => null,
                            'newData'     => json_encode($pesanan->toArray()),
                            'ip_address'  => request()->ip(),
                            'user_agent'  => request()->userAgent(),
                        ]);

                        return $pesanan;
                    });
                }),
        ];
    }
}