<?php

namespace App\Filament\Resources\Admin\AdminPemesanans\Tables;

use App\Filament\Tables\Actions\DetailPesananViewAction;
use App\Models\LogActivities;
use App\Models\Pesanan;
use App\Models\Task;
use App\Models\TaskActivity;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class AdminPemesanansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                Pesanan::query()
                    // Tambahkan with() agar query lebih cepat (mencegah N+1)
                    ->with(['tasks' => function ($query) {
                        $query->where('role', 'marketing');
                    }])
                    ->whereHas('tasks', function ($query) {
                        $query->where('role', 'marketing');
                    })
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

                TextColumn::make('no_requisition')
                    ->label('No Requisition')
                    ->placeholder('---:---')
                    ->default('---:---')
                    ->searchable(),
                    
                TextColumn::make('keranjang.sub_total')
                    ->label('Total Barang')
                    ->numeric()
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                    
                // PERBAIKAN: Ganti nama identifier agar tidak membaca seluruh array dari relasi
                TextColumn::make('status_marketing') 
                    ->label('Status Pemesanan')
                    ->badge()
                    ->getStateUsing(function (Pesanan $record): int {
                        // Ambil secara spesifik task yang memiliki role marketing saja
                        $task = $record->tasks->where('role', 'marketing')->first();
                        
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
            ])
            ->filters([
                // Tambahkan filter di sini jika diperlukan
            ])
            ->recordActions([

                DetailPesananViewAction::make(),


                // ACTION BARU: Cetak Surat Requisition

                    Action::make('terima_rilis_dana')
                        ->label('Validasi Rilis Dana')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        // Muncul jika invoice sudah ada dan belum dilunasi
                        ->hidden(fn (Pesanan $record): bool => in_array($record->status_perilisan_dana, [1, 2]))
                        ->requiresConfirmation()
                        ->modalHeading('Validasi Rilis Dana')
                        ->modalDescription(fn (Pesanan $record) => new HtmlString(
                            "Rilis dana untuk pesanan <strong>{$record->code}</strong>.<br><br>Apakah anda ingin menyetujui perilisan dana untuk pesanan ini?"
                        ))
                        
                        ->modalSubmitActionLabel('Ya, Setuju') 
                        ->modalCancelActionLabel('Batal')
                        ->action(function (Pesanan $record) {

                        // 1. Update Pesanan
                        $record->update(['status_perilisan_dana' => 2]);

                        Notification::make()
                            ->success()
                            ->title('Validasi Rilis Dana')
                            ->body('Pesanan telah disetujui untuk perilisan dana.')
                            ->send();
                    }),

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
