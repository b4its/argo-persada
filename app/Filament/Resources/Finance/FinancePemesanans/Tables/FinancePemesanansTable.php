<?php

namespace App\Filament\Resources\Finance\FinancePemesanans\Tables;

use App\Models\Pesanan;
use App\Models\Task;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Schemas\Schema;

class FinancePemesanansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                // Filter Pesanan HANYA jika memiliki Task dengan role 'finance'
                Pesanan::query()
                    ->whereHas('tasks', function ($query) {
                        $query->where('role', 'finance');
                    })
                    ->orderBy('created_at', 'desc')
            )
            ->columns([
                // 1. No. Pemesanan (Diambil dari field 'code')
                TextColumn::make('code')
                    ->label('No. Pemesanan')
                    ->searchable()
                    ->sortable(),

                // 2. Tanggal (Diambil dari created_at pesanan atau tanggal_rilis_dana)
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d/m/Y') // Format sesuai gambar: 26/04/2026
                    ->sortable(),

                // 3. Total Harga
                TextColumn::make('total_harga')
                    ->label('Total Harga')
                    ->money('IDR', locale: 'id') // Format Rupiah (Rp)
                    ->sortable(),

                // 4. Status (Badge) - Diambil dari relasi Task terbaru
                TextColumn::make('task_status') 
                    ->label('Task Status')
                    ->badge()
                    ->getStateUsing(function (Pesanan $record): int {
                        // Ambil secara spesifik task yang memiliki role finance saja
                        $task = $record->tasks->where('role', 'finance')->first();
                        
                        return $task ? (int) $task->status : 0;
                    })
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        0 => 'Dibuat',
                        1 => 'In Progress',
                        2 => 'Selesai',
                        default => 'Unknown',
                    })
                    ->color(fn (int $state): string => match ($state) {
                        0 => 'gray',
                        1 => 'warning',
                        2 => 'success',
                        default => 'gray',
                    })
                    ->icon(fn (int $state): string => match ($state) {
                        0 => 'heroicon-m-plus-circle',
                        1 => 'heroicon-m-clock',
                        2 => 'heroicon-m-check-badge',
                        default => 'heroicon-m-question-mark-circle',
                    }),
            // $table->tinyInteger('pesanan_status')->default(0)->comment('0: dibuat, 1: pending, 2: perlu rilis dana, 3: perlu penagihan, 4: selesai');

                    TextColumn::make('status_marketing') 
                        ->label('Pesan Status')
                        ->badge()
                        ->getStateUsing(function (Pesanan $record): int {
                            // Ambil secara spesifik task yang memiliki role finance saja
                            $task = $record->tasks->where('role', 'finance')->first();
                            
                            return $task ? (int) $task->taskActivities->last()->pesanan_status : 0;
                        })
                        ->formatStateUsing(fn (int $state): string => match ($state) {
                            0 => 'Dibuat',
                            1 => 'Pending',
                            2 => 'Perlu Rilis Dana',
                            3 => 'Perlu Penagihan',
                            4 => 'Selesai',
                            default => 'Unknown',
                        })
                        ->color(fn (int $state): string => match ($state) {
                            0 => 'gray',
                            1 => 'info',
                            2 => 'warning',
                            3 => 'danger',
                            4 => 'success',
                            default => 'gray',
                        })
                        ->icon(fn (int $state): string => match ($state) {
                            0 => 'heroicon-m-plus-circle',       // Dibuat
                            1 => 'heroicon-m-clock',             // Pending
                            2 => 'heroicon-m-banknotes',         // Perlu Rilis Dana (uang/dana)
                            3 => 'heroicon-m-document-text',     // Perlu Penagihan (dokumen tagihan/invoice)
                            4 => 'heroicon-m-check-circle',      // Selesai
                            default => 'heroicon-m-question-mark-circle',
                        }),


                

                // 5. No. Invoice
                TextColumn::make('no_invoice')
                    ->label('No. Invoice')
                    ->placeholder('---:---') // Tampilkan ---:--- jika null/kosong
                    ->default('---:---')
                    ->searchable(),
            ])
            ->filters([
                // Tambahkan filter jika diperlukan nanti
            ])
            ->recordActions([
                ViewAction::make()
                    // Menggunakan ulang form schema yang Anda buat agar tampilannya rapi
                    ->form(
                        \App\Filament\Resources\Marketing\MarketingPemesanans\Schemas\MarketingPemesananForm::configure(Schema::make())->getComponents()
                    )
                    // KUNCI: Mengisi ulang data Repeater (list_barang) dari tabel QueueKeranjang
                    ->mutateRecordDataUsing(function (array $data, Pesanan $record): array {
                        // Load relasi agar data tersedia
                        $record->load(['keranjang.queueKeranjang']);

                        // Mapping data ke bentuk array agar bisa dibaca oleh komponen Repeater
                        if ($record->keranjang && $record->keranjang->queueKeranjang) {
                            $data['list_barang'] = $record->keranjang->queueKeranjang->map(function ($item) {
                                return [
                                    'item_name' => $item->item_name,
                                    'quantity' => $item->quantity,
                                    'satuan' => $item->satuan,
                                    'modal' => $item->modal,
                                    'po' => $item->po,
                                    'supplier_name' => $item->supplier_name,
                                    'keterangan' => $item->keterangan,
                                ];
                            })->toArray();
                        }

                        // Mengisi tanggal pemesanan dengan waktu data dibuat (karena di DB tanggal_pemesanan tidak disimpan khusus)
                        $data['tanggal_pemesanan'] = $record->created_at;

                        return $data;
                    }), // Icon titik tiga (Actions) biasanya di-handle otomatis oleh Filament jika ada actions
            
                    
                    ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->paginated([10, 25, 50]); // Pagination standar
    }
}