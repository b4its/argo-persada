<?php

namespace App\Filament\Resources\Admin\AdminPemesanans\Pages;

use App\Filament\Resources\Admin\AdminPemesanans\AdminPemesananResource;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
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

class ListAdminPemesanans extends ListRecords
{
    protected static ?string $title = 'Daftar Pemesanan';
    protected static string $resource = AdminPemesananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Tambahkan Pesanan')
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
                                    'kode' => $barang['kode'],
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
                        $pesanan = Pesanan::create([
                            'user_id' => $data['user_id'],
                            'keranjang_id' => $keranjang->id,
                            'no_po' => $generate_po_number,
                            'code' => $data['code'],
                            'ppn' => $tax_amount,
                            'total_harga' => $totalKeseluruhan + $tax_amount,
                            'group_name' => $data['group_name'],
                            'company_name' => $data['company_name'],
                            'address' => $data['address'],
                        ]);

                        // LANGKAH 5: Buat Task yang dikhususkan untuk Finance
                        // $task = Task::create([
                        //     'pesanan_id' => $pesanan->id,
                        //     'title' => 'Verifikasi Pesanan ' . $pesanan->code,
                        //     'role' => 'finance',
                        //     'description' => 'Pesanan baru telah dibuat oleh Marketing. Mohon untuk melakukan perilisan dana.',
                        //     'due_date' => now()->addDays(7), // Estimasi batas waktu task, bisa disesuaikan
                        //     'status' => 0, // 0 sebagai penanda status awal (pending/baru)
                        // ]);

                        // // LANGKAH 6: Buat Task Activity dengan unique requisition_number (huruf kapital dan angka)
                        // TaskActivity::create([
                        //     'created_user_id' => $data['user_id'],
                        //     'updated_user_id' => $data['user_id'],
                        //     'task_id' => $task->id,
                        //     'note' => 'Pesanan baru berhasil dibuat dan akan diteruskan ke Finance.',
                        //     'pesanan_status' => 0, // 0 penanda awal
                        // ]);
                        $task = Task::create([
                            'pesanan_id' => $pesanan->id,
                            'title' => 'Melakukan cetak surat requisition untuk pesanan ' . $pesanan->code,
                            'role' => 'marketing',
                            'description' => 'Mohon untuk melakukan cetak surat requisition pada pesanan ' . $pesanan->code,
                            'due_date' => now()->addDays(7), // Estimasi batas waktu task, bisa disesuaikan
                            'status' => 0, // 0 sebagai penanda status awal (pending/baru)
                        ]);

                        // LANGKAH 6: Buat Task Activity dengan unique requisition_number (huruf kapital dan angka)
                        TaskActivity::create([
                            'created_user_id' => $data['user_id'],
                            'updated_user_id' => $data['user_id'],
                            'task_id' => $task->id,
                            'note' => 'Pesanan baru berhasil dibuat dan akan diteruskan untuk melakukan cetak surat requisition.',
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

                Action::make('cetak_surat')
                    ->label('Cetak Dokumen Pesanan')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->form([
                        Select::make('periode')
                            ->label('Pilih Periode')
                            ->options([
                                'minggu' => 'Minggu Ini',
                                'bulan' => 'Bulan Ini',
                                'tahun' => 'Tahun Ini',
                                'custom' => 'Pilih Tanggal',
                            ])
                            ->reactive()
                            ->required(),

                        DatePicker::make('start_date')
                            ->label('Tanggal Mulai')
                            ->visible(fn ($get) => $get('periode') === 'custom')
                            ->required(fn ($get) => $get('periode') === 'custom'),

                        DatePicker::make('end_date')
                            ->label('Tanggal Selesai')
                            ->visible(fn ($get) => $get('periode') === 'custom')
                            ->required(fn ($get) => $get('periode') === 'custom'),
                    ])
                    ->action(function (array $data) {
                        // Logika pengiriman data ke route
                        // Kita gunakan redirect manual karena URL dinamis berdasarkan input form
                        return redirect()->route('surat_pesanan.index', [
                            'periode' => $data['periode'],
                            'start_date' => $data['start_date'] ?? null,
                            'end_date' => $data['end_date'] ?? null,
                        ]);
                    })
                    ->modalHeading('Cetak Dokumen')
                    ->modalDescription('Pilih periode dokumen yang ingin dicetak.')
                    ->modalSubmitActionLabel('Ya, Cetak')
                    // ->iconButton()
        ];
    }
}
