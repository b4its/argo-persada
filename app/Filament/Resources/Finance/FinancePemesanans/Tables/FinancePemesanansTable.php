<?php

namespace App\Filament\Resources\Finance\FinancePemesanans\Tables;

use App\Filament\Tables\Actions\DetailPesananViewAction;
use App\Models\AkunKeuangan;
use App\Models\KasHarian;
use App\Models\LogActivities;
use App\Models\Pesanan;
use App\Models\Saldo;
use App\Models\Task; 
use App\Models\TaskActivity; 
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\ViewAction;
use Filament\Actions\Action; // Pastikan menggunakan Action dari Tables
use Filament\Notifications\Notification; 
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString; 
use Illuminate\Support\Str;

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

                // 2. Tanggal (Diambil dari created_at pesanan)
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),

                // 3. Total Harga
                TextColumn::make('total_harga')
                    ->label('Total Harga')
                    ->money('IDR', locale: 'id') 
                    ->sortable(),

                // 4. Status (Badge) - Diambil dari relasi Task terbaru
                TextColumn::make('task_status') 
                    ->label('Task Status')
                    ->badge()
                    ->getStateUsing(function (Pesanan $record): int {
                        $task = $record->tasks->where('role', 'finance')->last();
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

                TextColumn::make('status_finance') 
                    ->label('Pesan Status')
                    ->badge()
                    ->getStateUsing(function (Pesanan $record): int {
                        $task = $record->tasks->where('role', 'finance')->last();
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
                        0 => 'gray',      // Dibuat
                        1 => 'info',      // Pending
                        2 => 'warning',   // Butuh Aksi: Rilis Dana
                        3 => 'primary',   // Butuh Aksi: Cetak Invoice
                        4 => 'danger',    // Menunggu Uang Masuk: Penagihan
                        5 => 'success',   // Milestone: Lunas
                        6 => 'warning',   // Butuh Aksi: Logistik Surat Jalan
                        7 => 'info',      // Proses/Selesai Pengiriman
                        8 => 'success',   // Final: Semua Beres
                        default => 'gray',
                    })
                    ->icon(fn (int $state): string => match ($state) {
                        0 => 'heroicon-m-plus-circle',           // Dibuat
                        1 => 'heroicon-m-clock',                 // Pending
                        2 => 'heroicon-m-banknotes',             // Perlu Rilis Dana (uang)
                        3 => 'heroicon-m-printer',               // Cetak Invoice (printer)
                        4 => 'heroicon-m-document-text',         // Perlu Penagihan (dokumen tagihan)
                        5 => 'heroicon-m-check-badge',           // Ditandai Lunas (badge validasi)
                        6 => 'heroicon-m-document-duplicate',    // Cetak Surat Jalan (dokumen rangkap)
                        7 => 'heroicon-m-truck',                 // Selesai Dikirim (truk logistik)
                        8 => 'heroicon-m-check-circle',          // Selesai (centang final)
                        default => 'heroicon-m-question-mark-circle',
                    }),

                // 5. No. Invoice
                TextColumn::make('no_invoice')
                    ->label('No. Invoice')
                    ->placeholder('---:---')
                    ->default('---:---')
                    ->searchable(),
            ])
            ->filters([
                // Tambahkan filter jika diperlukan nanti
            ])
            ->recordActions([
                DetailPesananViewAction::make(),
            
                // ==========================================
                // ACTION 1: KONFIRMASI RILIS DANA
                // ==========================================
                Action::make('konfirmasi_rilis_dana')
                    ->label('Konfirmasi Rilis Dana')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    // Sembunyikan jika dana sudah dirilis
                    ->hidden(fn (Pesanan $record): bool => $record->tanggal_rilis_dana !== null || $record->status_perilisan_dana === 0 || $record->status_perilisan_dana === 1)
                    ->requiresConfirmation() 
                    ->modalHeading('Konfirmasi Rilis Dana')
                    ->modalDescription(fn (Pesanan $record) => new HtmlString(
                        "No Pemesanan:<br><strong>{$record->code}</strong><br><br>Apakah ingin konfirmasi rilis dana pada pesanan ini?"
                    ))
                    ->modalSubmitActionLabel('Iya') 
                    ->modalCancelActionLabel('Tidak') 
                    ->mountUsing(function (Action $action, Pesanan $record) {
                        $currentUserId = auth()->id();

                        $completedTask = Task::where('pesanan_id', $record->id)
                            ->where('role', 'finance')
                            ->where('status', 2) 
                            ->first();

                        if ($completedTask) {
                            $completedActivity = TaskActivity::where('task_id', $completedTask->id)
                                ->where('pesanan_status', 4) 
                                ->first(); 

                            if ($completedActivity) {
                                $namaUser = $completedActivity->updatedUser ? $completedActivity->updatedUser->name : 'Seseorang';

                                Notification::make()
                                    ->warning()
                                    ->title('Aksi Dibatalkan')
                                    ->body("Tugas ini sudah diselesaikan oleh {$namaUser}.")
                                    ->send();
                                
                                $action->cancel(); 
                                return; 
                            }
                        }

                        $isAlreadyInProgress = TaskActivity::whereHas('task', function ($query) use ($record) {
                                $query->where('pesanan_id', $record->id)
                                    ->where('role', 'finance');
                            })
                            ->where('pesanan_status', 1) 
                            ->exists();

                        if (!$isAlreadyInProgress) {
                            $currentTask = Task::where('pesanan_id', $record->id)
                                ->where('role', 'finance')
                                ->latest()
                                ->first();

                            if ($currentTask) {
                                $originalCreatorId = TaskActivity::whereHas('task', function($query) use ($record) {
                                        $query->where('pesanan_id', $record->id);
                                    })
                                    ->orderBy('created_at', 'asc')
                                    ->value('created_user_id') ?? $currentUserId;

                                $updatedTask = Task::updateOrCreate(
                                    ['id' => $currentTask->id],
                                    [
                                        'status' => 1, 
                                        'updated_at' => now(),
                                    ]
                                );

                                TaskActivity::create([
                                    'created_user_id' => $originalCreatorId,
                                    'updated_user_id' => $currentUserId,
                                    'task_id' => $currentTask->id, 
                                    'note' => 'Mempersiapkan Konfirmasi Rilis Dana untuk pesanan ' . $record->code,
                                    'pesanan_status' => 1, 
                                ]);

                                LogActivities::create([
                                    'user_id' => $currentUserId,
                                    'action' => 'Update Task - In Progress to Konfirmasi Rilis Dana',
                                    'description' => 'Finance melakukan proses untuk konfirmasi rilis dana pada pesanan ' . $record->code,
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

                        $originalCreatorId = TaskActivity::whereHas('task', function($query) use ($record) {
                                $query->where('pesanan_id', $record->id);
                            })
                            ->orderBy('created_at', 'asc')
                            ->value('created_user_id') ?? $currentUserId;

                        $originTask = Task::where('pesanan_id', $record->id)
                            ->where('role', 'finance')
                            ->latest()
                            ->first();

                        Pesanan::updateOrCreate(
                            ['id' => $record->id],
                            [
                                'tanggal_rilis_dana' => now(),
                                'updated_at' => now()
                            ] 
                        );

                        $latestTask = Task::where('pesanan_id', $record->id)
                            ->where('role', 'finance')
                            ->latest()
                            ->first();
                        
                        $currentTask = Task::where('pesanan_id', $record->id)
                            ->where('role', 'finance')
                            ->latest()
                            ->first();

                        if ($currentTask) {
                            $currentTask->update([
                                'status' => 1,
                            ]);
                        }

                        $currentAkunKeuangan = AkunKeuangan::firstOrCreate(
                            ['name' => "Barang Umum"], // Kriteria pencarian
                            ['kode' => "PBU-0-" . $record->id]               
                        );

                        // 1. Cari saldo terakhir dari database untuk dijadikan default saldo_awal
                        $lastTransaction = KasHarian::where('akun_keuangan_id', $currentAkunKeuangan->id)
                            ->latest('id')
                            ->first();

                        $saldoAwalOtomatis = $lastTransaction ? $lastTransaction->saldo_akhir : 0;

                        // 2. Buat record dengan saldo_awal yang sudah estafet
                        $currentKasHarian = KasHarian::create([
                            'company_internal_id' => $record->company_internal_id,
                            'user_id'             => $record->user_id, 
                            'akun_keuangan_id'    => $currentAkunKeuangan->id, 
                            'pesanan_id'          => $record->id,
                            'saldo_awal'          => $saldoAwalOtomatis, // Tidak kaku 0, tapi ambil saldo terakhir
                            'debet'               => 0,
                            'kredit'              => $record->total_harga,
                            'keterangan'          => "Pembelian Barang Umum" 
                        ]);

                        LogActivities::create([
                            'user_id' => $currentUserId,
                            'action' => 'Update Task - Konfirmasi Rilis Dana',
                            'description' => 'Finance melakukan konfirmasi rilis dana pada pesanan ' . $record->code,
                            'oldData' => json_encode($latestTask->toArray()),
                            'newData' => json_encode($currentTask->toArray()),
                            'ip_address' => request()->ip(),
                            'user_agent' => request()->userAgent(),
                        ]);
                        

                        $newTask = Task::create([
                            'pesanan_id' => $record->id,
                            'title' => 'Logistik diharapkan untuk melakukan cetak surat jalan pada pesanan ' . $record->code,
                            'role' => 'logistik',
                            'description' => 'akan diteruskan ke Logistik untuk cetak surat jalan pada pesanan ' . $record->code,
                            'due_date' => now(),
                            'status' => 0, 
                        ]);

                        $oldTaskActivity = TaskActivity::create([
                            'created_user_id' => $originalCreatorId, 
                            'updated_user_id' => $currentUserId, 
                            'task_id' => $originTask->id, 
                            'note' => 'Melakukan konfirmasi rilis dana untuk pesanan ' . $record->code . ' kemudian diteruskan ke Logistik untuk melakukan Cetak Surat Jalan.',
                            'pesanan_status' => 1, 
                        ]);

                        $newTaskActivity = TaskActivity::create([
                            'created_user_id' => $originalCreatorId, 
                            'updated_user_id' => $currentUserId, 
                            'task_id' => $newTask->id,
                            'note' => 'konfirmasi rilis dana kemudian diteruskan ke Logistik untuk melakukan Cetak Surat Jalan.',
                            'pesanan_status' => 6, 
                        ]);

                        LogActivities::create([
                            'user_id' => $currentUserId,
                            'action' => 'Create Task - Cetak Surat Jalan',
                            'description' => 'Finance melakukan perilisan dana kemudian menyuruh logistik untuk melakukan cetak surat jalan untuk pesanan ' . $record->code,
                            'oldData' => json_encode($oldTaskActivity->toArray()),
                            'newData' => json_encode($newTaskActivity->toArray()),
                            'ip_address' => request()->ip(),
                            'user_agent' => request()->userAgent(),
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Berhasil')
                            ->body('Konfirmasi Rilis Dana berhasil dan tugas diperbarui.')
                            ->send();
                    }),

                // ==========================================
                // ACTION 2: CETAK INVOICE
                // ==========================================
                Action::make('cetak_invoice')
                    ->label('Cetak Invoice')
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    // Hanya muncul jika Rilis Dana sudah dilakukan (dan logistik telah selesai, ditandai dari logistik membalikkan task ke finance)
                    // serta invoice belum dicetak
                    ->hidden(fn (Pesanan $record): bool => $record->tanggal_rilis_dana === null || $record->no_invoice !== null || $record->tanggal_terbit_surat_jalan === null || $record->tanggal_surat_kembali === null)
                    ->requiresConfirmation()
                    ->modalHeading('Cetak Invoice Pesanan')
                    ->modalDescription(fn (Pesanan $record) => new HtmlString(
                        "No Pemesanan:<br><strong>{$record->code}</strong><br><br>Apakah Anda yakin ingin menerbitkan Invoice untuk pesanan ini?"
                    ))
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('tanggal_jatuh_tempo')
                            ->label('Tenggat Waktu')
                            ->required()
                            ->native(false)
                    ])
                    ->modalSubmitActionLabel('Terbitkan Invoice') 
                    ->modalCancelActionLabel('Batal')
                    
                    // PERBAIKAN: Gunakan beforeFormFilled alih-alih mountUsing
                    ->beforeFormFilled(function (Action $action, Pesanan $record) {
                        $currentUserId = auth()->id();

                        // 1. Cek Pencegahan Spam
                        if ($record->no_invoice !== null) {
                            Notification::make()
                                ->warning()
                                ->title('Aksi Dibatalkan')
                                ->body('Invoice untuk pesanan ini sudah diterbitkan sebelumnya.')
                                ->send();
                            $action->cancel();
                            return;
                        }

                        $isAlreadyInProgress = TaskActivity::whereHas('task', function ($query) use ($record) {
                                $query->where('pesanan_id', $record->id)
                                    ->where('role', 'finance')
                                    ->where('title', 'like', '%cetak invoice%');
                            })
                            ->where('pesanan_status', 1) 
                            ->exists();

                        if (!$isAlreadyInProgress) {
                            $currentTask = Task::where('pesanan_id', $record->id)
                                ->where('role', 'finance')
                                ->where('title', 'like', '%cetak invoice%')
                                ->latest()
                                ->first();

                            if ($currentTask) {
                                $originalCreatorId = TaskActivity::whereHas('task', function($query) use ($record) {
                                        $query->where('pesanan_id', $record->id);
                                    })
                                    ->orderBy('created_at', 'asc')
                                    ->value('created_user_id') ?? $currentUserId;

                                $updatedTask = Task::updateOrCreate(
                                    ['id' => $currentTask->id],
                                    ['status' => 1, 'updated_at' => now()]
                                );

                                TaskActivity::create([
                                    'created_user_id' => $originalCreatorId,
                                    'updated_user_id' => $currentUserId,
                                    'task_id' => $currentTask->id, 
                                    'note' => 'Mempersiapkan dokumen untuk cetak invoice pada pesanan ' . $record->code,
                                    'pesanan_status' => 1, 
                                ]);

                                LogActivities::create([
                                    'user_id' => $currentUserId,
                                    'action' => 'Update Task - In Progress to Cetak Invoice',
                                    'description' => 'Finance sedang memproses penerbitan invoice pesanan ' . $record->code,
                                    'oldData' => json_encode($currentTask->toArray()),
                                    'newData' => json_encode($updatedTask->toArray()),
                                    'ip_address' => request()->ip(),
                                    'user_agent' => request()->userAgent(),
                                ]);
                            }
                        }
                    })
                    
                    // Action utama (Dieksekusi saat tombol Terbitkan Invoice di-klik)
                    ->action(function (Pesanan $record, array $data) {
                        $currentUserId = auth()->id();

                        $originalCreatorId = TaskActivity::whereHas('task', function($query) use ($record) {
                                $query->where('pesanan_id', $record->id);
                            })
                            ->orderBy('created_at', 'asc')
                            ->value('created_user_id') ?? $currentUserId;

                        // Generate nomor invoice
                        $generatedInvoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(5));
                        $oldPesananData = $record->toArray();

                        // Update tabel pesanan
                        $record->update([
                            'no_invoice' => $generatedInvoiceNumber,
                            'tanggal_terbit_invoice' => now(),
                            // Tanggal ini sudah aman tidak akan null lagi
                            'tanggal_jatuh_tempo' => $data['tanggal_jatuh_tempo'], 
                        ]);

                        $currentTask = Task::where('pesanan_id', $record->id)
                            ->where('role', 'finance')
                            ->latest()
                            ->first();

                        $oldTaskData = $currentTask ? $currentTask->toArray() : [];

                        if ($currentTask) {
                            $currentTask->update(['status' => 1]); // Tandai task sedang dalam penagihan
                            
                            TaskActivity::create([
                                'created_user_id' => $originalCreatorId, 
                                'updated_user_id' => $currentUserId, 
                                'task_id' => $currentTask->id, 
                                'note' => 'Invoice berhasil diterbitkan dengan nomor ' . $generatedInvoiceNumber . ' untuk pesanan ' . $record->code . ', kemudian finance melakukan penagihan.',
                                'pesanan_status' => 4, // 4 = perlu penagihan
                            ]);
                        }

                        LogActivities::create([
                            'user_id' => $currentUserId,
                            'action' => 'Generate Invoice',
                            'description' => 'Finance menerbitkan invoice ' . $generatedInvoiceNumber . ' untuk pesanan ' . $record->code,
                            'oldData' => json_encode(['pesanan' => $oldPesananData, 'task' => $oldTaskData]),
                            'newData' => json_encode(['pesanan' => $record->toArray(), 'task' => $currentTask ? $currentTask->toArray() : []]),
                            'ip_address' => request()->ip(),
                            'user_agent' => request()->userAgent(),
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Invoice Diterbitkan')
                            ->body('Invoice dibuat. Masuk masa penagihan.')
                            ->send();
                    }),

                // ==========================================
                // ACTION 3: TANDAI LUNAS
                // ==========================================
                Action::make('tandai_lunas')
                    ->label('Tandai Lunas')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    // Muncul jika invoice sudah ada dan belum dilunasi
                    ->hidden(fn (Pesanan $record): bool => $record->no_invoice === null || $record->tanggal_lunas !== null)
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pembayaran Lunas')
                    ->modalDescription(fn (Pesanan $record) => new HtmlString(
                        "Tagihan untuk Invoice <strong>{$record->no_invoice}</strong>.<br><br>Apakah uang pembayaran sudah diterima dan pesanan dianggap lunas?"
                    ))
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('tanggal_valid_lunas')
                            ->label('Tanggal Lunas')
                            ->required()
                            ->native(false)
                    ])
                    ->modalSubmitActionLabel('Ya, Lunas') 
                    ->modalCancelActionLabel('Batal')
                    ->action(function (Pesanan $record, array $data) {
                        $currentUserId = auth()->id();

                        $currentTask = Task::where('pesanan_id', $record->id)
                            ->where('role', 'finance')
                            ->latest()
                            ->first();
                        

                        $originalCreatorId = TaskActivity::whereHas('task', function($query) use ($record) {
                                $query->where('pesanan_id', $record->id);
                            })
                            ->orderBy('created_at', 'asc')
                            ->value('created_user_id') ?? $currentUserId;

                        // 1. Update Pesanan Lunas
                        $record->update([
                            'tanggal_lunas' => now(),
                            'validasi_tanggal_lunas'=> $data['tanggal_valid_lunas'],
                            'status_pesanan'=> 2, // selesai
                        ]);

                        // 2. Update Task Finance jadi Selesai (2)
                        $currentTask->update(['status' => 2]); 

                        // 3. Catat Task Activity Final (pesanan_status = 5)
                        TaskActivity::create([
                            'created_user_id' => $originalCreatorId, 
                            'updated_user_id' => $currentUserId, 
                            'task_id' => $currentTask->id, 
                            'note' => 'Pembayaran telah diterima. Pesanan dianggap LUNAS.',
                            'pesanan_status' => 5, // 5 = Ditandai Lunas
                        ]);

                        

                        // $keteranganKas = "Toko: " . $detailToko;

                        // ------------------------------------------------

                        DB::transaction(function () use ($record) {
                            // --- SOLUSI PENGAMBILAN DATA QUEUE KERANJANG ---
                        // Mengambil seluruh item dari queue_keranjang berdasarkan keranjang_id dari pesanan ini
                        $queueItems = \App\Models\QueueKeranjang::where('keranjang_id', $record->keranjang_id)->get();

                        $detailToko = $queueItems->map(function ($item) {
                            return "{$item->supplier_name}";
                        })->implode(', ');

                            $currentAkunKeuanganLunas = AkunKeuangan::firstOrCreate(
                                ['name' => "Barang Umum"],
                                ['kode' => "PBU-0-" . $record->id]               
                            );

                            // Cari saldo akhir terakhir untuk jadi saldo awal baris baru
                            $lastSaldo = KasHarian::where('akun_keuangan_id', $currentAkunKeuanganLunas->id)
                                ->latest('id')
                                ->value('saldo_akhir') ?? 0;

                            KasHarian::create([
                                'company_internal_id' => $record->company_internal_id,
                                'user_id'             => $record->user_id, 
                                'akun_keuangan_id'    => $currentAkunKeuanganLunas->id, 
                                'pesanan_id'          => $record->id,
                                'toko'                => $detailToko,
                                'saldo_awal'          => $lastSaldo, // <--- Menambahkan estafet saldo
                                'debet'               => $record->total_harga,
                                'kredit'              => 0,
                                'keterangan'          => "Penjualan Barang Umum",
                            ]);
                        });


                        // 4. Log Activity
                        LogActivities::create([
                            'user_id' => $currentUserId,
                            'action' => 'Tandai Lunas',
                            'description' => 'Finance menandai invoice ' . $record->no_invoice . ' LUNAS',
                            'oldData' => json_encode(['tanggal_lunas' => null]),
                            'newData' => json_encode(['tanggal_lunas' => now()]),
                            'ip_address' => request()->ip(),
                            'user_agent' => request()->userAgent(),
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Pembayaran Selesai')
                            ->body('Pesanan telah ditandai LUNAS.')
                            ->send();
                    }),
                    Action::make('cetak_invoice_finance')
                        ->label('Print Invoice')
                        ->icon('heroicon-o-printer')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Cetak Invoice Finance')
                        ->modalDescription('Apakah anda ingin mencetak dokumen Invoice Permintaan Finance ini?')
                        ->modalSubmitActionLabel('Ya, Cetak')
                        ->url(fn ($record) => route('invoice.request.finance.index', $record->id))
                        ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->paginated([10, 25, 50]); 
    }
}