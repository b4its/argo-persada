<?php

namespace App\Filament\Resources\Marketing\MarketingPemesanans\Tables;

use App\Models\Pesanan;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Schemas\Schema;

class MarketingPemesanansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                Pesanan::query()
                    ->selectRaw('pesanan.*, ROW_NUMBER() OVER (ORDER BY created_at desc) as row_num')
                    ->orderBy('created_at', 'desc')
            )
            ->columns([
                TextColumn::make('row_num')
                    ->label('No')
                    ->sortable(),

                TextColumn::make('code')
                    ->label('No Pemesanan')
                    ->sortable()
                    ->searchable(),
                    
                TextColumn::make('keranjang.sub_total')
                    ->label('Total Barang')
                    ->numeric()
                    ->money('IDR', locale: 'id') // Opsional: Format ke Rupiah
                    ->sortable(),
                    
                // PERBAIKAN: Mengambil data aman dari relasi bertingkat (HasMany -> HasMany)
                TextColumn::make('status_pemesanan')
                    ->label('Status Pemesanan')
                    ->badge()
                    ->getStateUsing(function (Pesanan $record): int {
                        // Ambil Task terakhir, lalu TaskActivity terakhir
                        $task = $record->tasks()->latest()->first();
                        if ($task) {
                            $activity = $task->taskActivities()->latest()->first();
                            if ($activity) {
                                return (int) $activity->pesanan_status;
                            }
                        }
                        return 0; // Default jika tidak ada data
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
                        1 => 'warning',
                        2 => 'info',
                        3 => 'danger',
                        4 => 'success',
                        default => 'gray',
                    })
                    ->icon(fn (int $state): string => match ($state) {
                        0 => 'heroicon-m-plus-circle',
                        1 => 'heroicon-m-clock',
                        2 => 'heroicon-m-banknotes',
                        3 => 'heroicon-m-exclamation-triangle',
                        4 => 'heroicon-m-check-badge',
                        default => 'heroicon-m-question-mark-circle',
                    }),
            ])
            ->filters([
                // Tambahkan filter di sini jika diperlukan
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
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}