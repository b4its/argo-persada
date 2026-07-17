<?php

namespace App\Filament\Resources\Logistik\LogistikPemesanans\Tables;

use App\Filament\Tables\Actions\DetailPesananViewAction;
use App\Models\LogActivities;
use App\Models\Pesanan;
use App\Models\Task; 
use App\Models\TaskActivity; 
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action; // Pastikan menggunakan Action dari
use Filament\Notifications\Notification; 
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString; 
use Illuminate\Support\Str;

class LogistikPemesanansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                // Filter Pesanan HANYA jika memiliki Task dengan role 'logistik'
                Pesanan::query()
                    ->whereHas('tasks', function ($query) {
                        $query->where('role', 'logistik');
                    })
                    ->orderBy('created_at', 'desc')
            )
            ->columns([
                // 1. No. Pemesanan
                TextColumn::make('code')
                    ->label('No. Pemesanan')
                    ->searchable()
                    ->sortable(),

                // 2. Tanggal
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),

                // 3. Total Harga
                TextColumn::make('total_harga')
                    ->label('Total Harga')
                    ->money('IDR', locale: 'id') 
                    ->sortable(),

                // 4. Status Task (Logistik)
                TextColumn::make('task_status') 
                    ->label('Task Logistik')
                    ->badge()
                    ->getStateUsing(function (Pesanan $record): int {
                        $task = $record->tasks->where('role', 'logistik')->last();
                        return $task ? (int) $task->status : 0;
                    })
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        0 => 'Pending',
                        1 => 'In Progress (Pengantaran)',
                        2 => 'Completed',
                        default => 'Unknown',
                    })
                    ->color(fn (int $state): string => match ($state) {
                        0 => 'gray',
                        1 => 'warning',
                        2 => 'success',
                        default => 'gray',
                    })
                    ->icon(fn (int $state): string => match ($state) {
                        0 => 'heroicon-m-clock',
                        1 => 'heroicon-m-truck',
                        2 => 'heroicon-m-check-badge',
                        default => 'heroicon-m-question-mark-circle',
                    }),

                // 5. Status Pesanan Keseluruhan
                TextColumn::make('status_pesanan') 
                    ->label('Status Pesanan')
                    ->badge()
                    ->getStateUsing(function (Pesanan $record): int {
                        $task = $record->tasks->where('role', 'logistik')->last();
                        return $task && $task->taskActivities->isNotEmpty() ? (int) $task->taskActivities->last()->pesanan_status : 0;
                    })
                    ->formatStateUsing(fn (int $state): string => match ($state) {
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
                    })
                    ->color(fn (int $state): string => match ($state) {
                        0 => 'gray',      
                        1 => 'info',      
                        2 => 'warning',   
                        3 => 'primary',   
                        4 => 'danger',    
                        5 => 'success',   
                        6 => 'warning',   
                        7 => 'info',      
                        8 => 'success',   
                        default => 'gray',
                    })
                    ->icon(fn (int $state): string => match ($state) {
                        0 => 'heroicon-m-plus-circle',           
                        1 => 'heroicon-m-clock',                 
                        2 => 'heroicon-m-banknotes',             
                        3 => 'heroicon-m-printer',               
                        4 => 'heroicon-m-document-text',         
                        5 => 'heroicon-m-check-badge',           
                        6 => 'heroicon-m-document-duplicate',    
                        7 => 'heroicon-m-truck',                 
                        8 => 'heroicon-m-check-circle',          
                        default => 'heroicon-m-question-mark-circle',
                    }),

                TextColumn::make('no_invoice')
                    ->label('No. Invoice')
                    ->placeholder('---:---')
                    ->default('---:---')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                DetailPesananViewAction::make(),

                // ==============================================================
                // ACTION 1: CETAK SURAT JALAN (Mengubah status ke In Progress)
                // ==============================================================
                Action::make('konfirmasi_terbit_surat_jalan')
                    ->label('Cetak Surat Jalan')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('warning')
                    // Sembunyikan jika surat jalan SUDAH diterbitkan
                    ->hidden(fn (Pesanan $record): bool => $record->tanggal_terbit_surat_jalan !== null)
                    ->requiresConfirmation() 
                    ->modalHeading('Konfirmasi Terbit Surat Jalan')
                    ->modalDescription(fn (Pesanan $record) => new HtmlString(
                        "No Pemesanan:<br><strong>{$record->code}</strong><br><br>Apakah ingin mencetak Surat Jalan dan siap melakukan pengantaran?"
                    ))
                    ->modalSubmitActionLabel('Iya') 
                    ->modalCancelActionLabel('Batal') 
                    ->action(function (Pesanan $record) {
                        $currentUserId = auth()->id();

                        $currentTask = Task::where('pesanan_id', $record->id)
                            ->where('role', 'logistik')
                            ->latest()
                            ->first();

                        if (!$currentTask) return;

                        $originalCreatorId = TaskActivity::where('task_id', $currentTask->id)
                            ->orderBy('created_at', 'asc')
                            ->value('created_user_id') ?? $currentUserId;
                        
                        $delivery_number = "DO-" .date('ymd') . '-' . strtoupper(Str::random(5));
                        // 1. Update Pesanan
                        $record->update([
                            'tanggal_terbit_surat_jalan' => now(),
                            'no_delivery_order' => $delivery_number
                        ]);

                        // 2. Update Task menjadi In Progress (1)
                        $currentTask->update([
                            'status' => 1,
                        ]);

                        // 3. Catat Task Activity (Status 6 = Cetak Surat Jalan)
                        TaskActivity::create([
                            'created_user_id' => $originalCreatorId, 
                            'updated_user_id' => $currentUserId, 
                            'task_id' => $currentTask->id, 
                            'note' => 'Surat Jalan telah dicetak. Pesanan sedang dalam proses pengantaran oleh Logistik.',
                            'pesanan_status' => 1, 
                        ]);

                        // 4. Log Activity
                        LogActivities::create([
                            'user_id' => $currentUserId,
                            'action' => 'Terbit Surat Jalan',
                            'description' => 'Logistik mencetak surat jalan untuk pesanan ' . $record->code,
                            'oldData' => json_encode(['tanggal_terbit_surat_jalan' => null, 'status_task' => 0]),
                            'newData' => json_encode(['tanggal_terbit_surat_jalan' => now(), 'status_task' => 1]),
                            'ip_address' => request()->ip(),
                            'user_agent' => request()->userAgent(),
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Surat Jalan Terbit')
                            ->body('Status pesanan kini dalam proses pengantaran.')
                            ->send();
                    }),

                // ==============================================================
                // ACTION 2: SELESAI DIKIRIM (Mengubah status ke Completed)
                // ==============================================================
                Action::make('konfirmasi_selesai_dikirim')
                    ->label('Tandai Selesai Dikirim')
                    ->icon('heroicon-o-truck')
                    ->color('success')
                    // Sembunyikan jika Surat Jalan BELUM terbit ATAU Pengantaran SUDAH selesai
                    ->hidden(fn (Pesanan $record): bool => 
                        $record->tanggal_terbit_surat_jalan === null || 
                        $record->tanggal_surat_kembali !== null
                    )
                    ->requiresConfirmation() 
                    ->modalHeading('Konfirmasi Pengiriman Selesai')
                    ->modalDescription(fn (Pesanan $record) => new HtmlString(
                        "No Pemesanan:<br><strong>{$record->code}</strong><br><br>Apakah barang sudah diterima oleh pemesan?"
                    ))
                    ->modalSubmitActionLabel('Ya, Selesai Dikirim') 
                    ->modalCancelActionLabel('Batal') 
                    ->action(function (Pesanan $record) {
                        $currentUserId = auth()->id();

                        $currentTask = Task::where('pesanan_id', $record->id)
                            ->where('role', 'logistik')
                            ->latest()
                            ->first();

                        if (!$currentTask) return;

                        $originalCreatorId = TaskActivity::where('task_id', $currentTask->id)
                            ->orderBy('created_at', 'asc')
                            ->value('created_user_id') ?? $currentUserId;

                        // 1. Update Pesanan (Surat Jalan Kembali / Selesai)
                        $record->update([
                            'tanggal_surat_kembali' => now()
                        ]);

                        // 2. Update Task menjadi Completed (2)
                        $currentTask->update([
                            'status' => 2,
                        ]);

                        $financeTask = Task::where('pesanan_id', $record->id)
                            ->where('role', 'finance')
                            ->latest()
                            ->first();

                        // 3. Catat Task Activity (Status 7 = Selesai Dikirim)
                        TaskActivity::create([
                            'created_user_id' => $originalCreatorId, 
                            'updated_user_id' => $currentUserId, 
                            'task_id' => $currentTask->id, 
                            'note' => 'Barang telah berhasil dikirim dan diterima oleh pemesan.',
                            'pesanan_status' => 7, 
                        ]);
                        
                        TaskActivity::create([
                            'created_user_id' => $originalCreatorId, 
                            'updated_user_id' => $currentUserId, 
                            'task_id' => $financeTask->id, 
                            'note' => 'Finance perlu cetak invoice dengan kode psanan ' . $record->code,
                            'pesanan_status' => 3, 
                        ]);

                        // 4. Log Activity
                        LogActivities::create([
                            'user_id' => $currentUserId,
                            'action' => 'Pengiriman Selesai',
                            'description' => 'Logistik menandai pesanan ' . $record->code . ' telah selesai dikirim.',
                            'oldData' => json_encode(['tanggal_surat_kembali' => null, 'status_task' => 1]),
                            'newData' => json_encode(['tanggal_surat_kembali' => now(), 'status_task' => 2]),
                            'ip_address' => request()->ip(),
                            'user_agent' => request()->userAgent(),
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Pengiriman Selesai')
                            ->body('Tugas logistik untuk pesanan ini telah selesai.')
                            ->send();
                    }),

                    Action::make('cetak_surat_jalan')
                        ->label('Print Surat Jalan')
                        ->icon('heroicon-o-printer')
                        ->color('info')
                        ->hidden(fn (Pesanan $record): bool => $record->tanggal_terbit_surat_jalan === null)
                        ->modalHeading('Pilih Item untuk Surat Jalan')
                        ->modalDescription(fn (Pesanan $record) => new HtmlString(
                            "Pesanan: <strong>{$record->code}</strong><br>Centang item yang akan dikirim."
                        ))
                        ->form(fn (Pesanan $record) => [
                            \Filament\Forms\Components\CheckboxList::make('selected_items')
                                ->label('Pilih Item Yang Akan Dikirim')
                                ->options(function () use ($record) {
                                    $items = \App\Models\QueueKeranjang::where('keranjang_id', $record->keranjang_id)->get();
                                    return $items->pluck('item_name', 'id')->map(function ($name, $id) use ($items) {
                                        $item = $items->find($id);
                                        return "{$item->item_name} ({$item->quantity} {$item->satuan})";
                                    })->toArray();
                                })
                                ->columns(1)
                                ->required()
                                ->validationMessages([
                                    'required' => 'Pilih minimal satu item untuk dicetak di surat jalan.',
                                ]),
                            \Filament\Forms\Components\Textarea::make('keterangan_logistik')
                                ->label('Keterangan Logistik')
                                ->placeholder('Isi keterangan untuk surat jalan...')
                                ->columnSpanFull()
                                ->default($record->keterangan_logistik),
                        ])
                        ->modalSubmitActionLabel('Cetak')
                        ->action(function (Pesanan $record, array $data) {
                            $selectedIds = $data['selected_items'] ?? [];
                            $keterangan = $data['keterangan_logistik'] ?? null;
                            $record->update(['keterangan_logistik' => $keterangan]);
                            $idsParam = implode(',', $selectedIds);
                            $url = route('surat_jalan.index', [
                                'id' => $record->id,
                                'item_ids' => $idsParam,
                                'back' => url()->previous(),
                            ]);
                            return redirect()->to($url);
                        }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->paginated([10, 25, 50]);
    }
}