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
                ->form(
                    \App\Filament\Resources\Marketing\MarketingPemesanans\Schemas\MarketingPemesananForm::configure(Schema::make())->getComponents()
                )
                ->mutateFormDataUsing(function (array $data): array {
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

                        // LANGKAH 4: Buat Pesanan utama
                        $pesanan = Pesanan::create([
                            'user_id' => $data['user_id'],
                            'keranjang_id' => $keranjang->id,
                            'code' => $data['code'],
                            'ppn' => $tax_amount,
                            'total_harga' => $totalKeseluruhan + $tax_amount,
                            'group_name' => $data['group_name'],
                            'company_name' => $data['company_name'],
                            'address' => $data['address'],
                        ]);

                        // LANGKAH 5: Buat Task yang dikhususkan untuk Finance
                        $task = Task::create([
                            'pesanan_id' => $pesanan->id,
                            'title' => 'Verifikasi Pesanan ' . $pesanan->code,
                            'role' => 'finance',
                            'description' => 'Pesanan baru telah dibuat oleh Marketing. Mohon untuk melakukan perilisan dana.',
                            'due_date' => now()->addDays(7), // Estimasi batas waktu task, bisa disesuaikan
                            'status' => 0, // 0 sebagai penanda status awal (pending/baru)
                        ]);

                        // LANGKAH 6: Buat Task Activity dengan unique requisition_number (huruf kapital dan angka)
                        TaskActivity::create([
                            'created_user_id' => $data['user_id'],
                            'updated_user_id' => $data['user_id'],
                            'task_id' => $task->id,
                            'note' => 'Pesanan baru berhasil dibuat dan akan diteruskan ke Finance.',
                            'pesanan_status' => 0, // 0 penanda awal
                        ]);

                        // LANGKAH 7: Catat Log Activities
                        LogActivities::create([
                            'user_id' => $data['user_id'],
                            'action' => 'Create Pesanan',
                            'description' => 'Marketing membuat pesanan baru dengan kode ' . $pesanan->code,
                            'oldData' => null,
                            'newData' => json_encode($pesanan->toArray()),
                            'ip_address' => request()->ip(),
                            'user_agent' => request()->userAgent(),
                        ]);

                        // Return pesanan agar proses create di Filament dinyatakan sukses
                        return $pesanan;
                    });
                }),
        ];
    }
}