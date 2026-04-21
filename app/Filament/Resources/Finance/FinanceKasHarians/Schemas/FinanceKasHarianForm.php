<?php

namespace App\Filament\Resources\Finance\FinanceKasHarians\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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
                    ->preload()
                    ->required(),

                TextInput::make('saldo_awal')
                    ->label('Saldo Awal')
                    ->prefix('Rp')
                    // Gunakan mask untuk tampilan ribuan yang cantik
                    ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                    ->stripCharacters('.')
                    ->required()
                    ->live(onBlur: true),
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

                Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->required()
                    ->columnSpanFull()
                
            ]);
    }
}
