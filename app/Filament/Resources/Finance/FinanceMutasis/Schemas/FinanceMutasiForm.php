<?php

namespace App\Filament\Resources\Finance\FinanceMutasis\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class FinanceMutasiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Buku Besar')
                    ->description('Periode dan detail utama buku besar mutasi.')
                    ->schema([
                        // TextInput::make('code')
                        //     ->label('Kode Buku Besar')
                        //     ->required()
                        //     ->unique(ignoreRecord: true)
                        //     ->maxLength(255),
                            
                        TextInput::make('name')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255),
                            
                        // TextInput::make('type')
                        //     ->label('Tipe')
                        //     ->maxLength(255),
                            
                        DatePicker::make('periode')
                            ->label('Periode')
                            ->displayFormat('d/m/Y'),
                        // sementara hapus
                        // Select::make("id_pesanan")
                        //     ->label("Pemesanan")
                        //     ->options(function () {
                        //         return \App\Models\Pesanan::pluck('code', 'id');
                        //     })
                        //     ->searchable()
                        //     ->required(),   
                    ]),

                Section::make('Daftar Akun Mutasi')
                    ->description('Kelompok mutasi berdasarkan akun (Contoh: 1100-00-020 Kas).')
                    ->schema([
                        Repeater::make('mutasis')
                            ->relationship('mutasis')
                            ->label('')
                            ->addActionLabel('Tambah Akun Mutasi')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['code'] ?? 'Akun Baru')
                            ->schema([
                                TextInput::make('code')
                                    ->label('Kode Akun')
                                    ->required()
                                    ->placeholder('Misal: 1100-00-020'),
                                    
                                TextInput::make('name')
                                    ->label('Nama Akun')
                                    ->required()
                                    ->placeholder('Misal: Kas'),

                                // SALDO AWAL DAN AKHIR DIPINDAHKAN KE SINI
                                // TextInput::make('saldo_awal')
                                //     ->label('Saldo Awal')
                                //     ->numeric()
                                //     ->default(0.00)
                                //     ->prefix('Rp'),

                                TextInput::make('saldo_awal')
                                    ->label('Saldo Awal')
                                    ->prefix('Rp')
                                    // Gunakan mask untuk tampilan ribuan yang cantik
                                    ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                                    ->stripCharacters('.')
                                    ->required()
                                    ->live(onBlur: true) // Gunakan onBlur agar tidak berat saat mengetik
                                    ->columnSpanFull(),
                                    
                                // TextInput::make('saldo_akhir')
                                //     ->label('Saldo Akhir')
                                //     ->numeric()
                                //     ->default(0.00)
                                //     ->prefix('Rp')
                                //     ->helperText('Hasil akhir kalkulasi dari seluruh baris transaksi di bawah.'),

                                Repeater::make('mutasiItems')
                                    ->relationship('mutasiItems')
                                    ->label('Detail Transaksi')
                                    ->addActionLabel('Tambah Baris Transaksi')
                                    ->schema([
                                        TextInput::make('no_ref')
                                            ->label('No. Ref')
                                            ->maxLength(255),
                                            
                                        Textarea::make('keterangan')
                                            ->label('Keterangan')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                            
                                        // TextInput::make('debet')
                                        //     ->label('Debet')
                                        //     ->numeric()
                                        //     ->default(0.00)
                                        //     ->prefix('Rp'),

                                        TextInput::make('debet')
                                            ->label('Debet')
                                            ->prefix('Rp')
                                            // Gunakan mask untuk tampilan ribuan yang cantik
                                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                                            ->stripCharacters('.')
                                            ->required()
                                            ->live(onBlur: true) 
                                            ->columnSpanFull(),
                                            
                                        // TextInput::make('kredit')
                                        //     ->label('Kredit')
                                        //     ->numeric()
                                        //     ->default(0.00)
                                        //     ->prefix('Rp'),

                                        TextInput::make('kredit')
                                            ->label('Kredit')
                                            ->prefix('Rp')
                                            // Gunakan mask untuk tampilan ribuan yang cantik
                                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                                            ->stripCharacters('.')
                                            ->required()
                                            ->live(onBlur: true) 
                                            ->columnSpanFull(),
                                            
                                        // // HANYA MENYISAKAN FIELD SALDO DI SINI
                                        // TextInput::make('saldo')
                                        //     ->label('Saldo')
                                        //     ->numeric()
                                        //     ->default(0.00)
                                        //     ->prefix('Rp')
                                        //     ->helperText('Saldo berjalan pada baris ini.'),
                                    ])
                                    ->defaultItems(1)
                                    ->reorderableWithButtons()
                            ])
                            ->defaultItems(0)
                    ])
            ]);
    }
}