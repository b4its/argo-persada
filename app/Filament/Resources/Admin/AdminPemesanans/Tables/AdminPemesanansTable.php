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
                    ->with(['tasks', 'user'])
                    ->orderBy('created_at', 'desc')
            )
            ->columns([
                TextColumn::make('code')
                    ->label('No Pemesanan')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('tipe_pesanan')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        0 => 'Supply',
                        1 => 'Projek',
                        default => '-',
                    })
                    ->color(fn (int $state): string => match ($state) {
                        0 => 'info',
                        1 => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('user.name')
                    ->label('Dibuat Oleh')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                TextColumn::make('no_requisition')
                    ->label('No Requisition')
                    ->placeholder('---:---')
                    ->default('---:---')
                    ->searchable(),

                TextColumn::make('no_po')
                    ->label('No PO')
                    ->searchable(),
                    
                TextColumn::make('keranjang.sub_total')
                    ->label('Total')
                    ->numeric()
                    ->money('IDR', locale: 'id')
                    ->sortable(),

                TextColumn::make('status_marketing')
                    ->label('Marketing')
                    ->badge()
                    ->getStateUsing(fn (Pesanan $record): int => (int) ($record->tasks->where('role', 'marketing')->first()?->status ?? 0))
                    ->formatStateUsing(fn (int $state): string => match ($state) { 0 => 'Pending', 1 => 'Proses', 2 => 'Selesai', default => '-' })
                    ->color(fn (int $state): string => match ($state) { 0 => 'gray', 1 => 'warning', 2 => 'success', default => 'gray' }),

                TextColumn::make('status_finance')
                    ->label('Finance')
                    ->badge()
                    ->getStateUsing(fn (Pesanan $record): int => (int) ($record->tasks->where('role', 'finance')->first()?->status ?? 0))
                    ->formatStateUsing(fn (int $state): string => match ($state) { 0 => 'Pending', 1 => 'Proses', 2 => 'Selesai', default => '-' })
                    ->color(fn (int $state): string => match ($state) { 0 => 'gray', 1 => 'warning', 2 => 'success', default => 'gray' }),

                TextColumn::make('status_logistik')
                    ->label('Logistik')
                    ->badge()
                    ->getStateUsing(fn (Pesanan $record): int => (int) ($record->tasks->where('role', 'logistik')->first()?->status ?? 0))
                    ->formatStateUsing(fn (int $state): string => match ($state) { 0 => 'Pending', 1 => 'Proses', 2 => 'Selesai', default => '-' })
                    ->color(fn (int $state): string => match ($state) { 0 => 'gray', 1 => 'warning', 2 => 'success', default => 'gray' }),

                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                // Tambahkan filter di sini jika diperlukan
            ])
            ->recordActions([

                DetailPesananViewAction::make(),

                Action::make('view_tracking')
                    ->label('Tracking')
                    ->icon('heroicon-m-rectangle-stack')
                    ->color('info')
                    ->button()
                    ->modalHeading(fn (Pesanan $record) => 'Tracking Operasional: ' . $record->code)
                    ->modalDescription('Histori tugas dari Marketing → Finance → Logistik')
                    ->modalWidth('7xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->infolist(function (Pesanan $record) {
                        $record->loadMissing(['tasks.taskActivities.createdUser', 'tasks.taskActivities.updatedUser']);
                        return [
                            \Filament\Infolists\Components\RepeatableEntry::make('tasks')
                                ->hiddenLabel()
                                ->schema([
                                    \Filament\Schemas\Components\Section::make(fn ($record) => '▸ ' . strtoupper($record->role) . ' : ' . $record->title)
                                        ->schema([
                                            \Filament\Schemas\Components\Grid::make(3)->schema([
                                                \Filament\Infolists\Components\TextEntry::make('role')
                                                    ->label('Divisi')
                                                    ->badge()
                                                    ->color(fn ($state) => match ($state) {
                                                        'marketing' => 'info',
                                                        'finance' => 'success',
                                                        'logistik' => 'warning',
                                                        default => 'gray',
                                                    }),
                                                \Filament\Infolists\Components\TextEntry::make('status')
                                                    ->label('Status')
                                                    ->badge()
                                                    ->formatStateUsing(fn ($state) => match ((int) $state) {
                                                        0 => 'Pending', 1 => 'In Progress', 2 => 'Selesai', default => 'Unknown',
                                                    })
                                                    ->color(fn ($state) => match ((int) $state) {
                                                        0 => 'gray', 1 => 'warning', 2 => 'success', default => 'gray',
                                                    }),
                                                \Filament\Infolists\Components\TextEntry::make('created_at')
                                                    ->label('Dibuat')
                                                    ->dateTime('d M Y, H:i'),
                                            ]),
                                            \Filament\Infolists\Components\RepeatableEntry::make('taskActivities')
                                                ->label('Riwayat Eksekusi')
                                                ->schema([
                                                    \Filament\Schemas\Components\Grid::make(4)->schema([
                                                        \Filament\Infolists\Components\TextEntry::make('createdUser.name')
                                                            ->label('Oleh')->default('System')->weight('bold'),
                                                        \Filament\Infolists\Components\TextEntry::make('updatedUser.name')
                                                            ->label('Diperbarui')->default('-'),
                                                        \Filament\Infolists\Components\TextEntry::make('pesanan_status')
                                                            ->label('Tahapan')
                                                            ->badge()
                                                            ->formatStateUsing(fn ($state) => match ((int) $state) {
                                                                0 => 'Dibuat', 1 => 'Pending', 2 => 'Perlu Rilis Dana',
                                                                3 => 'Perlu Cetak Invoice', 4 => 'Perlu Penagihan',
                                                                5 => 'Ditandai Lunas', 6 => 'Cetak Surat Jalan',
                                                                7 => 'Selesai Dikirim', 8 => 'Selesai', default => 'Unknown',
                                                            }),
                                                        \Filament\Infolists\Components\TextEntry::make('created_at')
                                                            ->label('Waktu')->dateTime('d M Y, H:i:s'),
                                                    ]),
                                                    \Filament\Infolists\Components\TextEntry::make('note')
                                                        ->label('Catatan')->columnSpanFull(),
                                                ]),
                                        ])
                                        ->collapsible()
                                ]),
                        ];
                    }),

                    Action::make('terima_rilis_dana')
                        ->label('Validasi Rilis Dana')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        // Muncul jika invoice sudah ada dan belum dilunasi
                        ->hidden(fn (Pesanan $record): bool => in_array($record->status_perilisan_dana, [2, 3]) || $record -> status_perilisan_dana === 0)
                        ->requiresConfirmation()
                        ->modalHeading('Validasi Rilis Dana')
                        ->modalDescription(fn (Pesanan $record) => new HtmlString(
                            "Rilis dana untuk pesanan <strong>{$record->code}</strong>.<br><br>Apakah anda ingin menyetujui perilisan dana untuk pesanan ini?"
                        ))
                        
                        ->modalSubmitActionLabel('Ya, Setuju') 
                        ->modalCancelActionLabel('Batal')
                        ->action(function (Pesanan $record) {

                        // 1. Update Pesanan
                        $record->update(['status_perilisan_dana' => 3, 'status_pesanan' => 1]);

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
