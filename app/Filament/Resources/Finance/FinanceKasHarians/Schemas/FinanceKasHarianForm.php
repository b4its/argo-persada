<?php

namespace App\Filament\Resources\Finance\FinanceKasHarians\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class FinanceKasHarianForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                Select::make('company_internal_id')
                    ->label('PT')
                    // Gunakan nama fungsi relasi di Model Pesanan yang baru saja kita ubah
                    ->relationship('companyInternal', 'name') 
                    // Format tampilan option-nya (Nama Perusahaan - SINGKATAN)
                    ->getOptionLabelFromRecordUsing(fn (\Illuminate\Database\Eloquent\Model $record) => "{$record->name} - {$record->singkatan}")
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('akun_keuangan_id')
                    ->label('Akun Keuangan')
                    ->relationship('akunKeuangan', 'name') // Pastikan 'name' adalah kolom yang merepresentasikan nama akun di tabel akun_keuangan
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        // Form ini akan muncul sebagai modal ketika user mengetikkan akun yang tidak ada dan menekan enter/tombol tambah
                        TextInput::make('name')
                            ->label('Nama Akun')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('kode')
                            ->label('Kode Akun')
                            ->required()
                            ->maxLength(255),
                        
                    ])
                    ->getOptionLabelFromRecordUsing(fn (\Illuminate\Database\Eloquent\Model $record) => "{$record->name} - {$record->kode} ") // Opsional: Sesuaikan format tampilan jika butuh lebih dari sekadar nama
                    ->required(),

                Select::make('pesanan_id')
                    ->label('Pesanan')
                    // Gunakan nama fungsi relasi di Model Pesanan yang baru saja kita ubah
                    ->relationship('pesanan', 'code') 
                    // Format tampilan option-nya (Nama Perusahaan - SINGKATAN)
                    ->getOptionLabelFromRecordUsing(fn (\Illuminate\Database\Eloquent\Model $record) => "{$record->code} - {$record->no_delivery_order}")
                    ->searchable()
                    ->preload(),

                TextInput::make('saldo_awal')
                    ->label('Saldo Awal')
                    ->prefix('Rp')
                    // Gunakan mask untuk tampilan ribuan yang cantik
                    ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                    ->stripCharacters('.')
                    ->numeric()
                    ->minValue(0)
                    ->required()
                    ->live(onBlur: true),
                TextInput::make('debet')
                    ->label('Debet')
                    ->prefix('Rp')
                    // Gunakan mask untuk tampilan ribuan yang cantik
                    ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                    ->stripCharacters('.')
                    ->numeric()
                    ->minValue(0)
                    ->required()
                    ->live(onBlur: true),
                    
                TextInput::make('kredit')
                    ->label('Kredit')
                    ->prefix('Rp')
                    // Gunakan mask untuk tampilan ribuan yang cantik
                    ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                    ->stripCharacters('.')
                    ->numeric()
                    ->minValue(0)
                    ->required()
                    ->live(onBlur: true)
                    // Kredit (uang keluar) tidak boleh melebihi saldo yang tersedia
                    ->rules([
                        function (Get $get) {
                            return function (string $attribute, $value, \Closure $fail) use ($get) {
                                $saldoAwal = (float) ($get('saldo_awal') ?? 0);
                                $debet = (float) ($get('debet') ?? 0);
                                $kredit = (float) $value;

                                if (($saldoAwal + $debet - $kredit) < 0) {
                                    $fail("Kredit (Rp " . number_format($kredit, 0, ',', '.') . ") melebihi saldo yang tersedia (Rp " . number_format($saldoAwal + $debet, 0, ',', '.') . "). Saldo tidak boleh minus.");
                                }
                            };
                        },
                    ]),

                
                Select::make('metode_pembayaran')
                    ->label('Metode Pembayaran')
                    ->options([
                        0 => 'Belum Ditentukan',
                        1 => 'Tunai',
                        2 => 'Kredit',
                    ])
                    ->default(0)
                    ->native(false),

                Select::make('kategori')
                        ->label('Kategori')
                        ->options([
                            1 => 'Penjualan',
                            2 => 'Piutang',
                            3 => 'Biaya Umum dan Administrasi Kantor',
                            4 => 'Biaya Lain Lain',
                        ])
                        ->default(1)
                        ->native(false) 
                        ->required(),

                Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->required()
                    ->columnSpanFull()
                
            ]);
    }
}
