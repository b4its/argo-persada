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
use Filament\Forms\Components\Textarea;
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

                TextColumn::make('tanggal_po')
                    ->label('Tanggal PO')
                    ->date('d/m/Y')
                    ->default(fn (Pesanan $record) => $record->created_at?->format('d/m/Y'))
                    ->sortable(),

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

                TextColumn::make('status_perilisan_dana')
                    ->label('Persetujuan Rilis Dana')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ((int) $state) {
                        0 => 'Belum Diajukan',
                        1 => 'Menunggu Persetujuan',
                        2 => 'Ditolak',
                        3 => 'Disetujui',
                        default => '-',
                    })
                    ->color(fn ($state) => match ((int) $state) {
                        0 => 'gray',
                        1 => 'warning',
                        2 => 'danger',
                        3 => 'success',
                        default => 'gray',
                    })
                    ->icon(fn ($state) => match ((int) $state) {
                        0 => 'heroicon-m-minus-circle',
                        1 => 'heroicon-m-clock',
                        2 => 'heroicon-m-x-circle',
                        3 => 'heroicon-m-check-circle',
                        default => 'heroicon-m-minus-circle',
                    }),

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
                        ->label('Setujui Rilis Dana')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->visible(fn (Pesanan $record): bool => $record->status_perilisan_dana === 1)
                        ->requiresConfirmation()
                        ->modalHeading('Persetujuan Rilis Dana (Admin)')
                        ->modalDescription(fn (Pesanan $record) => new HtmlString(
                            "Rilis dana untuk pesanan <strong>{$record->code}</strong>.<br>Total Tagihan: <strong>Rp " . number_format($record->total_harga, 0, ',', '.') . "</strong><br><br>Apakah Anda menyetujui perilisan dana untuk pesanan ini?"
                        ))
                        ->modalSubmitActionLabel('Ya, Setujui') 
                        ->modalCancelActionLabel('Batal')
                        ->action(function (Pesanan $record) {
                            $currentUserId = auth()->id();

                            $record->update(['status_perilisan_dana' => 3, 'status_pesanan' => 1]);

                            $task = Task::where('pesanan_id', $record->id)
                                ->where('role', 'finance')
                                ->latest()
                                ->first();

                            if ($task) {
                                TaskActivity::create([
                                    'created_user_id' => $currentUserId,
                                    'updated_user_id' => $currentUserId,
                                    'task_id' => $task->id,
                                    'note' => 'Admin menyetujui pengajuan rilis dana pesanan ' . $record->code . '.',
                                    'pesanan_status' => 2,
                                ]);
                            }

                            LogActivities::create([
                                'user_id' => $currentUserId,
                                'action' => 'Admin Approve Rilis Dana',
                                'description' => 'Admin menyetujui perilisan dana untuk pesanan ' . $record->code,
                                'oldData' => json_encode(['status_perilisan_dana' => 1]),
                                'newData' => json_encode(['status_perilisan_dana' => 3]),
                                'ip_address' => request()->ip(),
                                'user_agent' => request()->userAgent(),
                            ]);

                            Notification::make()
                                ->success()
                                ->title('Persetujuan Berhasil')
                                ->body('Pesanan telah disetujui untuk perilisan dana.')
                                ->send();
                        }),

                    Action::make('tolak_rilis_dana')
                        ->label('Tolak Rilis Dana')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn (Pesanan $record): bool => $record->status_perilisan_dana === 1)
                        ->form([
                            Textarea::make('alasan_penolakan')
                                ->label('Alasan Penolakan')
                                ->placeholder('Masukkan alasan penolakan rilis dana...')
                                ->required()
                                ->rows(3),
                        ])
                        ->modalHeading('Tolak Pengeluaran Dana (Admin)')
                        ->modalDescription(fn (Pesanan $record) => new HtmlString(
                            "Pengajuan rilis dana untuk pesanan <strong>{$record->code}</strong> akan ditolak."
                        ))
                        ->modalSubmitActionLabel('Tolak Pengajuan')
                        ->modalCancelActionLabel('Batal')
                        ->action(function (Pesanan $record, array $data) {
                            $currentUserId = auth()->id();
                            $alasan = $data['alasan_penolakan'] ?? 'Tidak ada alasan';

                            $record->update(['status_perilisan_dana' => 2]);

                            $task = Task::where('pesanan_id', $record->id)
                                ->where('role', 'finance')
                                ->latest()
                                ->first();

                            if ($task) {
                                TaskActivity::create([
                                    'created_user_id' => $currentUserId,
                                    'updated_user_id' => $currentUserId,
                                    'task_id' => $task->id,
                                    'note' => 'Pengajuan rilis dana DITOLAK oleh Admin: ' . $alasan,
                                    'pesanan_status' => 1,
                                ]);
                            }

                            LogActivities::create([
                                'user_id' => $currentUserId,
                                'action' => 'Admin Tolak Rilis Dana',
                                'description' => 'Admin menolak rilis dana untuk pesanan ' . $record->code . '. Alasan: ' . $alasan,
                                'oldData' => json_encode(['status_perilisan_dana' => 1]),
                                'newData' => json_encode(['status_perilisan_dana' => 2, 'alasan' => $alasan]),
                                'ip_address' => request()->ip(),
                                'user_agent' => request()->userAgent(),
                            ]);

                            Notification::make()
                                ->warning()
                                ->title('Rilis Dana Ditolak')
                                ->body('Pengajuan rilis dana telah ditolak.')
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
