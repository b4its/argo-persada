<?php

namespace App\Filament\Widgets\Admin;

use App\Filament\Tables\Actions\DetailPesananViewAction;
use App\Models\Pesanan;
use Filament\Actions\Action;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Database\Eloquent\Builder;

class AdminTaskTables extends TableWidget
{
    protected static ?string $heading = 'Riwayat Pesanan dan Tugas';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn (): Builder => Pesanan::query()
                    ->whereHas('tasks')
                    ->with([
                        'tasks', 
                        'tasks.taskActivities.createdUser'
                    ])
                    ->latest('updated_at')
            )
            ->columns([
                TextColumn::make('no_po')
                    ->label('No. Pesanan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Nomor pesanan disalin')
                    ->icon('heroicon-m-hashtag'),

                TextColumn::make('company_name')
                    ->label('Perusahaan')
                    ->searchable()
                    ->limit(30),

                TextColumn::make('tasks_count')
                    ->label('Total Tugas')
                    ->getStateUsing(fn (Pesanan $record): int => $record->tasks->count())
                    ->badge()
                    ->color('gray'),

                TextColumn::make('completed_tasks')
                    ->label('Tugas Selesai')
                    ->getStateUsing(fn (Pesanan $record): int => $record->tasks->where('status', 2)->count())
                    ->badge()
                    ->color('success'),

                TextColumn::make('updated_at')
                    ->label('Terakhir Update')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->description(fn (Pesanan $record): string => $record->updated_at?->diffForHumans() ?? ''),
            ])
            ->actions([
                DetailPesananViewAction::make(),

                Action::make('view_log')
                    ->label('Lihat Detail Aktifitas')
                    ->icon('heroicon-m-rectangle-stack')
                    ->color('info')
                    ->button() 
                    ->modalHeading(fn (Pesanan $record) => 'Detail Operasional Pesanan: ' . $record->code)
                    ->modalDescription('Daftar tugas beserta log riwayat aktivitas eksekusi.')
                    ->modalWidth('5xl')
                    ->modalSubmitAction(false) 
                    ->modalCancelActionLabel('Tutup')
                    // MENGGUNAKAN INFOLIST (Optimal untuk Read-Only)
                    ->infolist([
                        RepeatableEntry::make('tasks')
                            ->hiddenLabel()
                            ->schema([
                                // Section ini akan otomatis mengambil data per Task
                                Section::make(fn ($record) => 'Tugas: ' . $record->title)
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextEntry::make('role')
                                                ->label('Tim Penanggung Jawab')
                                                ->badge(),
                                            TextEntry::make('status')
                                                ->label('Status Tugas')
                                                ->badge()
                                                ->formatStateUsing(fn ($state) => match ((int) $state) {
                                                    0 => 'Pending',
                                                    1 => 'In Progress',
                                                    2 => 'Selesai',
                                                    default => 'Unknown',
                                                })
                                                ->color(fn ($state) => match ((int) $state) {
                                                    0 => 'gray',
                                                    1 => 'warning',
                                                    2 => 'success',
                                                    default => 'gray',
                                                }),
                                        ]),
                                        
                                        // Nested Repeatable membaca relasi taskActivities secara instan
                                        RepeatableEntry::make('taskActivities')
                                            ->label('Riwayat Aktivitas')
                                            ->schema([
                                                Grid::make(3)->schema([
                                                    TextEntry::make('createdUser.name')
                                                        ->label('Dieksekusi Oleh')
                                                        ->default('System')
                                                        ->weight('bold'),
                                                    
                                                    TextEntry::make('pesanan_status')
                                                        ->label('Status Pesanan (Saat Itu)')
                                                        ->badge()
                                                        ->formatStateUsing(fn ($state) => match ((int) $state) {
                                                            0 => 'Dibuat',
                                                            1 => 'Pending',
                                                            2 => 'Perlu Rilis Dana',
                                                            3 => 'Perlu Cetak Invoice',
                                                            4 => 'Perlu Penagihan',
                                                            5 => 'Ditandai Lunas',
                                                            6 => 'Cetak Surat Jalan',
                                                            7 => 'Tandai Selesai Dikirim',
                                                            8 => 'Selesai',
                                                            default => 'Unknown Status (' . $state . ')',
                                                        }),
                                                    
                                                    TextEntry::make('created_at')
                                                        ->label('Waktu Eksekusi')
                                                        ->dateTime('d M Y, H:i:s')
                                                        ->color('gray'),
                                                ]),
                                                TextEntry::make('note')
                                                    ->label('Catatan Log')
                                                    ->columnSpanFull(),
                                            ])
                                    ])
                                    ->collapsible()
                            ])
                    ]),
            ])
            ->paginated([5, 10, 25, 50])
            ->defaultSort('updated_at', 'desc');
    }
}