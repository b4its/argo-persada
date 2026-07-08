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
    protected static ?string $heading = 'Riwayat Pesanan dan Tracking Tugas (Hulu ke Hilir)';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn (): Builder => Pesanan::query()
                    ->whereHas('tasks')
                    ->with([
                        'tasks.taskActivities.createdUser',
                        'tasks.taskActivities.updatedUser',
                        'user',
                    ])
                    ->latest('updated_at')
            )
            ->columns([
                TextColumn::make('code')
                    ->label('No. Pesanan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Nomor pesanan disalin')
                    ->icon('heroicon-m-hashtag'),

                TextColumn::make('user.name')
                    ->label('Dibuat Oleh')
                    ->searchable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('company_name')
                    ->label('Perusahaan')
                    ->searchable()
                    ->limit(30),

                TextColumn::make('marketing_status')
                    ->label('Marketing')
                    ->getStateUsing(fn (Pesanan $record): string => $this->getRoleTaskStatus($record, 'marketing'))
                    ->badge()
                    ->color(fn ($state): string => match ($state) { 'Selesai' => 'success', 'Proses' => 'warning', default => 'gray' }),

                TextColumn::make('finance_status')
                    ->label('Finance')
                    ->getStateUsing(fn (Pesanan $record): string => $this->getRoleTaskStatus($record, 'finance'))
                    ->badge()
                    ->color(fn ($state): string => match ($state) { 'Selesai' => 'success', 'Proses' => 'warning', default => 'gray' }),

                TextColumn::make('logistik_status')
                    ->label('Logistik')
                    ->getStateUsing(fn (Pesanan $record): string => $this->getRoleTaskStatus($record, 'logistik'))
                    ->badge()
                    ->color(fn ($state): string => match ($state) { 'Selesai' => 'success', 'Proses' => 'warning', default => 'gray' }),

                TextColumn::make('completed_by')
                    ->label('Penyelesaian Terakhir')
                    ->getStateUsing(function (Pesanan $record): string {
                        $lastActivity = $record->tasks
                            ->flatMap->taskActivities
                            ->sortByDesc('created_at')
                            ->first();
                        return $lastActivity?->createdUser?->name 
                            ?? $lastActivity?->updatedUser?->name 
                            ?? '-';
                    })
                    ->description(function (Pesanan $record): string {
                        $lastActivity = $record->tasks
                            ->flatMap->taskActivities
                            ->sortByDesc('created_at')
                            ->first();
                        return $lastActivity ? $lastActivity->created_at->diffForHumans() : '';
                    }),

                TextColumn::make('updated_at')
                    ->label('Update')
                    ->dateTime('d M y, H:i')
                    ->sortable(),
            ])
            ->actions([
                DetailPesananViewAction::make(),

                Action::make('view_log')
                    ->label('Tracking Aktifitas')
                    ->icon('heroicon-m-rectangle-stack')
                    ->color('info')
                    ->button() 
                    ->modalHeading(fn (Pesanan $record) => 'Tracking Operasional: ' . $record->code)
                    ->modalDescription('Histori tugas dari Marketing → Finance → Logistik (hulu ke hilir)')
                    ->modalWidth('7xl')
                    ->modalSubmitAction(false) 
                    ->modalCancelActionLabel('Tutup')
                    ->infolist([
                        RepeatableEntry::make('tasks')
                            ->hiddenLabel()
                            ->schema([
                                Section::make(fn ($record) => '▸ ' . strtoupper($record->role) . ' : ' . $record->title)
                                    ->schema([
                                        Grid::make(3)->schema([
                                            TextEntry::make('role')
                                                ->label('Divisi')
                                                ->badge()
                                                ->color(fn ($state) => match ($state) {
                                                    'marketing' => 'info',
                                                    'finance' => 'success',
                                                    'logistik' => 'warning',
                                                    default => 'gray',
                                                }),
                                            TextEntry::make('status')
                                                ->label('Status')
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
                                            TextEntry::make('created_at')
                                                ->label('Dibuat')
                                                ->dateTime('d M Y, H:i'),
                                        ]),
                                        
                                        RepeatableEntry::make('taskActivities')
                                            ->label('Riwayat Eksekusi')
                                            ->schema([
                                                Grid::make(4)->schema([
                                                    TextEntry::make('createdUser.name')
                                                        ->label('Oleh')
                                                        ->default('System')
                                                        ->weight('bold')
                                                        ->color('primary'),
                                                    
                                                    TextEntry::make('updatedUser.name')
                                                        ->label('Diperbarui Oleh')
                                                        ->default('-'),

                                                    TextEntry::make('pesanan_status')
                                                        ->label('Tahapan')
                                                        ->badge()
                                                        ->formatStateUsing(fn ($state) => match ((int) $state) {
                                                            0 => 'Dibuat',
                                                            1 => 'Pending',
                                                            2 => 'Perlu Rilis Dana',
                                                            3 => 'Perlu Cetak Invoice',
                                                            4 => 'Perlu Penagihan',
                                                            5 => 'Ditandai Lunas',
                                                            6 => 'Cetak Surat Jalan',
                                                            7 => 'Selesai Dikirim',
                                                            8 => 'Selesai',
                                                            default => 'Unknown',
                                                        }),
                                                    
                                                    TextEntry::make('created_at')
                                                        ->label('Waktu')
                                                        ->dateTime('d M Y, H:i:s')
                                                        ->color('gray'),
                                                ]),
                                                TextEntry::make('note')
                                                    ->label('Catatan')
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

    private function getRoleTaskStatus(Pesanan $record, string $role): string
    {
        $task = $record->tasks->where('role', $role)->first();
        if (!$task) return '-';
        return match ((int) $task->status) {
            0 => 'Pending',
            1 => 'Proses',
            2 => 'Selesai',
            default => '-',
        };
    }
}