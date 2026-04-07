<?php

namespace App\Filament\Resources\Finance\FinanceMutasis\Tables;

use App\Models\BukuBesar;
use Carbon\Carbon;
use Filament\Forms\Components\Repeater; // Tambahkan import Repeater
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Schemas\Components\Fieldset; // Gunakan Forms\Components\Fieldset
use Filament\Support\RawJs;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class FinanceMutasisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                BukuBesar::query()
                    ->where("type", "mutasi")
                    ->orderBy('created_at', 'desc')
            )
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('periode')
                    ->label('Periode')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return null;
                        
                        // Menggunakan Carbon agar bisa menampilkan nama bulan (Januari, Februari, dst)
                        return Carbon::parse($state)->translatedFormat('F Y');
                    })
                    ->disabled(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                // 1. TOMBOL VIEW (MUNCUL JIKA BULAN/TAHUN SUDAH LEWAT)
                ViewAction::make()
                    ->visible(function (Model $record) {
                        if (!$record->periode) return false;
                        return Carbon::parse($record->periode)->format('Y-m') !== now()->format('Y-m');
                    }),

                // 2. TOMBOL EDIT (HANYA MUNCUL DI BULAN YANG SAMA)
                EditAction::make()
                    ->color('warning')
                    ->visible(function (Model $record) {
                        if (!$record->periode) return true;
                        return Carbon::parse($record->periode)->format('Y-m') === now()->format('Y-m');
                    })
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['type'] = 'mutasi'; // Memastikan type tidak berubah
                        return $data;
                    })
                    ->after(function (Model $record) {
                        foreach ($record->mutasis as $mutasi) {
                            $saldoAwal = $mutasi->saldo_awal ?? 0;
                            $currentSaldo = $saldoAwal;

                            $items = $mutasi->mutasiItems()->orderBy('id', 'asc')->get();
                            
                            foreach ($items as $item) {
                                $debet = $item->debet ?? 0;
                                $kredit = $item->kredit ?? 0;
                                $currentSaldo = $currentSaldo + $debet - $kredit;
                                $item->update(['saldo' => $currentSaldo]);
                            }

                            $mutasi->update(['saldo_akhir' => $currentSaldo]);
                        }
                    }),

                // 3. TOMBOL TAMBAH AKUN MUTASI
                Action::make('tambahkan_akun_mutasi')
                    ->label('Tambah Akun Mutasi')
                    ->icon('heroicon-o-plus')
                    ->color('success')
                    ->visible(function (Model $record) {
                        if (!$record->periode) return true;
                        return Carbon::parse($record->periode)->format('Y-m') === now()->format('Y-m');
                    })
                    ->form([
                        Fieldset::make('Informasi Akun')
                            ->schema([
                                TextInput::make('code')
                                    ->label('Kode Akun')
                                    ->required()
                                    ->placeholder('Misal: 1100-00-020'),
                                TextInput::make('name')
                                    ->label('Nama Akun')
                                    ->required()
                                    ->placeholder('Misal: Kas'),
                                TextInput::make('saldo_awal')
                                    ->label('Saldo Awal')
                                    ->prefix('Rp')
                                    // Gunakan mask untuk tampilan ribuan yang cantik
                                    ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                                    ->stripCharacters('.')
                                    ->required()
                                    ->live(onBlur: true),
                            ])->columns(3),

                        Fieldset::make('Detail Transaksi Awal')
                            ->schema([
                                // MENGGUNAKAN REPEATER AGAR BISA DITAMBAH BERKALI-KALI
                                Repeater::make('mutasiItems')
                                    ->label('Detail Mutasi')
                                    ->addActionLabel('Tambah Baris Transaksi')
                                    ->schema([
                                        TextInput::make('no_ref')
                                            ->label('No. Ref')
                                            ->maxLength(255),
                                        Textarea::make('keterangan')
                                            ->label('Keterangan')
                                            ->columnSpanFull()
                                            ->rows(3),
                                        TextInput::make('debet')
                                            ->label('Debet')
                                            ->prefix('Rp')
                                            // Gunakan mask untuk tampilan ribuan yang cantik
                                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                                            ->stripCharacters('.')
                                            ->required()
                                            ->live(onBlur: true),
                                        TextInput::make('kredit')
                                            ->label('Kredit')
                                            ->prefix('Rp')
                                            // Gunakan mask untuk tampilan ribuan yang cantik
                                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                                            ->stripCharacters('.')
                                            ->required()
                                            ->live(onBlur: true),
                                    ])
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->defaultItems(1) // Munculkan minimal 1 baris
                            ]),
                    ])
                    ->action(function (array $data, Model $record) {
                        // 1. Buat Mutasi (Akun)
                        $mutasi = $record->mutasis()->create([
                            'code' => $data['code'],
                            'name' => $data['name'],
                            'saldo_awal' => $data['saldo_awal'] ?? 0,
                            'saldo_akhir' => 0, 
                        ]);

                        $saldoAwal = $mutasi->saldo_awal;
                        $currentSaldo = $saldoAwal;

                        // 2. Looping data mutasiItems dari Repeater
                        if (!empty($data['mutasiItems'])) {
                            foreach ($data['mutasiItems'] as $item) {
                                $debet = $item['debet'] ?? 0;
                                $kredit = $item['kredit'] ?? 0;
                                
                                // Kalkulasi saldo berjalan
                                $currentSaldo = $currentSaldo + $debet - $kredit;

                                // 3. Simpan Mutasi Item
                                $mutasi->mutasiItems()->create([
                                    'no_ref' => $item['no_ref'] ?? null,
                                    'keterangan' => $item['keterangan'] ?? null,
                                    'debet' => $debet,
                                    'kredit' => $kredit,
                                    'saldo' => $currentSaldo,
                                ]);
                            }
                        }

                        // 4. Update Saldo Akhir di Tabel Parent (Mutasi)
                        $mutasi->update([
                            'saldo_akhir' => $currentSaldo
                        ]);
                    })
                    ->modalHeading('Tambah Akun Mutasi Baru')
                    ->modalSubmitActionLabel('Simpan Akun'),
                    
                // 4. TOMBOL CETAK BUKU BESAR (TETAP MUNCUL SETIAP SAAT)
                Action::make('cetak_buku_besar_mutasi')
                    ->label('Cetak Buku Besar Mutasi')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Cetak Buku Besar Mutasi')
                    ->modalDescription('Apakah anda ingin mencetak Buku Besar Mutasi ini?')
                    ->modalSubmitActionLabel('Ya, Cetak')
                    ->url(fn ($record) => route('buku_besar_mutasi.index', $record->id))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}