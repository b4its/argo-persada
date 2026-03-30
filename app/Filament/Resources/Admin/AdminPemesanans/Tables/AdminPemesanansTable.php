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
                Action::make('cetak_surat_requisition')
                    ->label('Cetak Surat Requisition')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->requiresConfirmation() // Memunculkan pop-up/modal
                    ->modalHeading('Cetak Surat Requisition')
                    ->modalDescription(fn (Pesanan $record) => new HtmlString(
                        "No Pemesanan:<br><strong>{$record->code}</strong><br><br>Apakah ingin cetak surat requisition pada pesanan ini?"
                    ))
                    ->hidden(fn (Pesanan $record) => $record->no_requisition !== null && $record->no_requisition !== '---:---')
                    ->modalSubmitActionLabel('Iya') // Kanan
                    ->modalCancelActionLabel('Tidak') // Kiri
                    ->mountUsing(function (Action $action, Pesanan $record) {
                        $currentUserId = auth()->id();

                        // 1. CEK APAKAH SUDAH SELESAI (Pencegahan Spam Modal Muncul)
                        $completedTask = Task::where('pesanan_id', $record->id)
                            ->where('role', 'marketing')
                            ->where('status', 2) // 2 = completed
                            ->first();

                        if ($completedTask) {
                            $completedActivity = TaskActivity::where('task_id', $completedTask->id)
                                ->where('pesanan_status', 8) // 4 = selesai
                                ->first(); 

                            if ($completedActivity) {
                                $namaUser = $completedActivity->updatedUser ? $completedActivity->updatedUser->name : 'Seseorang';

                                Notification::make()
                                    ->warning()
                                    ->title('Aksi Dibatalkan')
                                    ->body("Tugas ini sudah diselesaikan oleh {$namaUser}.")
                                    ->send();
                                
                                $action->cancel(); // Batalkan kemunculan modal
                                return; 
                            }
                        }

                        // 2. CEK APAKAH SUDAH PERNAH DIBUAT UNTUK STATUS IN PROGRESS
                        // Cek langsung ke TaskActivity yang menempel di Task terkait
                        $isAlreadyInProgress = TaskActivity::whereHas('task', function ($query) use ($record) {
                                $query->where('pesanan_id', $record->id)
                                    ->where('role', 'marketing');
                            })
                            ->where('pesanan_status', 1) // 1 = In Progress
                            ->exists();

                        // 3. PROSES BELAKANG LAYAR (Hanya insert activity jika BELUM ADA)
                        if (!$isAlreadyInProgress) {
                            // AMBIL TASK YANG SUDAH ADA (TIDAK CREATE TASK BARU)
                            $currentTask = Task::where('pesanan_id', $record->id)
                                ->where('role', 'marketing')
                                ->latest()
                                ->first();

                            // Hanya proses jika Task utamanya memang sudah ada sebelumnya
                            if ($currentTask) {
                                $originalCreatorId = TaskActivity::whereHas('task', function($query) use ($record) {
                                        $query->where('pesanan_id', $record->id);
                                    })
                                    ->orderBy('created_at', 'asc')
                                    ->value('created_user_id') ?? $currentUserId;

                                $updatedTask = Task::updateOrCreate(
                                    ['id' => $currentTask->id],
                                    [
                                        'status' => 1, // Update status menjadi In Progress
                                        'updated_at' => now(),
                                    ]
                                );

                                // HANYA Insert Task Activity Baru ke Task yang sudah ada
                                TaskActivity::create([
                                    'created_user_id' => $originalCreatorId,
                                    'updated_user_id' => $currentUserId,
                                    'task_id' => $currentTask->id, // Pakai ID dari task yang existing
                                    'note' => 'Mempersiapkan Cetak Surat Requisition untuk pesanan ' . $record->code,
                                    'pesanan_status' => 1, // 1 = In Progress
                                ]);

                                LogActivities::create([
                                    'user_id' => $currentUserId,
                                    'action' => 'Update Task - In Progress to Cetak Surat Requisition',
                                    'description' => 'Marketing melakukan proses untuk cetak surat requisition pada pesanan ' . $record->code,
                                    'oldData' => json_encode($currentTask->toArray()),
                                    'newData' => json_encode($updatedTask->toArray()),
                                    'ip_address' => request()->ip(),
                                    'user_agent' => request()->userAgent(),
                                ]);

                            }
                        }
                    })
                    ->action(function (Pesanan $record) {
                        $currentUserId = auth()->id();

                        // Cari pembuat awal (created_user_id)
                        $originalCreatorId = TaskActivity::whereHas('task', function($query) use ($record) {
                                $query->where('pesanan_id', $record->id);
                            })
                            ->orderBy('created_at', 'asc')
                            ->value('created_user_id') ?? $currentUserId;

                        $originTask = Task::where('pesanan_id', $record->id)
                            ->where('role', 'marketing')
                            ->latest()
                            ->first();
                        $noRequisition = Str::upper(Str::random(6)); // Generate No Requisition unik
                        // Update No Requisition
                        Pesanan::updateOrCreate(
                            ['id' => $record->id],
                            [
                                'no_requisition' => $noRequisition,
                                'updated_at' => now()
                            ] 
                        );

                        $latestTask = Task::where('pesanan_id', $record->id)
                            ->where('role', 'marketing')
                            ->latest()
                            ->first();
                        

                        $currentTask = Task::where('pesanan_id', $record->id)
                            ->where('role', 'marketing')
                            ->latest()
                            ->first();

                        if ($currentTask) {
                            $currentTask->update([
                                'status' => 2,
                            ]);
                        }

                        LogActivities::create([
                            'user_id' => $currentUserId,
                            'action' => 'Update Task - Cetak Surat Requisition',
                            'description' => 'Marketing melakukan cetak surat requisition pada pesanan ' . $record->code,
                            'oldData' => json_encode($latestTask->toArray()),
                            'newData' => json_encode($currentTask->toArray()),
                            'ip_address' => request()->ip(),
                            'user_agent' => request()->userAgent(),
                        ]);

                        // 3. Insert Data Task Baru (Hanya dibuat setelah proses Complete / klik "Iya")
                        $newTask = Task::create([
                            'pesanan_id' => $record->id,
                            'title' => 'Finance diharapkan untuk melakukan perilisan dana pada pesanan ' . $record->code . ', dengan No Requisition ' . ($noRequisition ?? '---:---'),
                            'role' => 'finance',
                            'description' => 'akan diteruskan ke Finance untuk proses perilisan dana.',
                            'due_date' => now(),
                            'status' => 0, // 0 = Created
                        ]);

                        // 4. Insert Data Task Activity Baru
                        TaskActivity::create([
                            'created_user_id' => $originalCreatorId, 
                            'updated_user_id' => $currentUserId, 
                            'task_id' => $originTask->id, // Pakai ID dari task yang existing
                            'note' => 'Melakukan pencetakan surat requisition',
                            'pesanan_status' => 8, // 8 = 8 selesai
                        ]);

                        TaskActivity::create([
                            'created_user_id' => $originalCreatorId, 
                            'updated_user_id' => $currentUserId, 
                            'task_id' => $newTask->id,
                            'note' => 'Mempersiapkan pesanan kemudian diteruskan ke Finance untuk melakukan perilisan dana.',
                            'pesanan_status' => 2, // 2 = perlu rilis dana
                        ]);

                        LogActivities::create([
                            'user_id' => $currentUserId,
                            'action' => 'Create Task - Verifikasi Pesanan ke Finance',
                            'description' => 'Marketing melakukan verifikasi pesanan untuk melakukan perilisan dana ke finance pada pesanan ' . $record->code,
                            'oldData' => json_encode($currentTask->toArray()),
                            'newData' => json_encode($newTask->toArray()),
                            'ip_address' => request()->ip(),
                            'user_agent' => request()->userAgent(),
                        ]);

                        // 5. Beri Notifikasi Sukses
                        Notification::make()
                            ->success()
                            ->title('Berhasil')
                            ->body('Surat requisition berhasil dicetak dan data tugas diperbarui.')
                            ->send();
                    }),
                    Action::make('cetak_surat_po')
                        ->label('Cetak Surat Pre Order')
                        ->icon('heroicon-o-printer')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Cetak Surat PO')
                        ->modalDescription('Apakah anda ingin mencetak dokumen Surat PO ini?')
                        ->modalSubmitActionLabel('Ya, Cetak')
                        ->url(fn ($record) => route('surat_po.index', ['id' => $record->id]))
                        ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
