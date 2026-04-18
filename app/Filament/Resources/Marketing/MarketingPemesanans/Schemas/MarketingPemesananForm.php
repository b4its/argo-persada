<?php

namespace App\Filament\Resources\Marketing\MarketingPemesanans\Schemas;

use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Support\RawJs;

class MarketingPemesananForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('tanggal_pemesanan')
                    ->label('Tanggal Pemesanan*')
                    ->required()
                    ->native(false)
                    ->dehydrated(false),

                Select::make('company_internal_id')
                    ->label('Perusahaan Internal')
                    // Gunakan nama fungsi relasi di Model Pesanan yang baru saja kita ubah
                    ->relationship('companyInternal', 'name') 
                    // Format tampilan option-nya (Nama Perusahaan - SINGKATAN)
                    ->getOptionLabelFromRecordUsing(fn (\Illuminate\Database\Eloquent\Model $record) => "{$record->name} - {$record->singkatan}")
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('saldo_id')
                    ->label('Saldo Awal')
                    // Gunakan nama fungsi relasi di Model Pesanan yang baru saja kita ubah
                    ->relationship('saldo', 'name') 
                    // Format tampilan option-nya (Nama Perusahaan - SINGKATAN)
                    ->getOptionLabelFromRecordUsing(fn (\Illuminate\Database\Eloquent\Model $record) => "{$record->name} - {$record->singkatan}")
                    ->searchable()
                    ->preload()
                    ->required(),
                    
                TextInput::make('group_name')
                    ->label('Group*')
                    ->placeholder('Masukkan Nama Group...')
                    ->required()
                    ->maxLength(255),

                TextInput::make('company_name')
                    ->label('Company*')
                    ->placeholder('Masukkan Nama Company...')
                    ->required()
                    ->maxLength(255),

                Textarea::make('address')
                    ->label('Alamat Pengiriman*')
                    ->placeholder('Masukkan Alamat Pengiriman...')
                    ->required()
                    ->maxLength(255),

                TextInput::make('user_id')
                    ->default(fn () => auth()->id())
                    ->hidden(),
                    
                TextInput::make('code')
                    ->default(fn() => 'PO-' . time())
                    ->hidden(),

                Repeater::make('list_barang')
                    ->label('List Barang')
                    ->addActionLabel('Tambah Barang Lagi')
                    // PERBAIKAN: Baris ->dehydrated(false) DIHAPUS di sini agar datanya dikirim ke Controller
                    ->minItems(1)
                    ->schema([
                        TextInput::make('item_name')
                            ->label('Nama Barang*')
                            ->placeholder('Masukkan Nama Barang...')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('quantity')
                            ->label('Quantity*')
                            ->numeric()
                            ->default(0)
                            ->required(),

                        TextInput::make('satuan')
                            ->label('Satuan*')
                            ->placeholder('Masukkan Satuan Barang...')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('modal')
                            ->label('Harga Beli (Modal)*')
                            ->prefix('Rp') 
                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)')) // Format angka Indonesia
                            ->stripCharacters('.') // Buang titik sebelum masuk ke database
                            ->numeric()
                            ->default(0)
                            ->required(),

                        TextInput::make('po')
                            ->label('Harga Jual (PO)*')
                            ->prefix('Rp') 
                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)')) // Format angka Indonesia
                            ->stripCharacters('.') // Buang titik sebelum masuk ke database
                            ->numeric()
                            ->default(0)
                            ->required(),

                        TextInput::make('supplier_name')
                            ->label('Supplier*')
                            ->placeholder('Masukkan Nama Supplier...')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('keterangan')
                            ->label('Keterangan')
                            ->placeholder('Masukkan Keterangan...')
                            ->maxLength(255),
                    ])
                    ->itemLabel(fn (array $state): ?string => $state['item_name'] ? 'Barang - ' . $state['item_name'] : 'Barang Baru')
                    ->collapsible()
                    ->columnSpanFull()
                    ->defaultItems(1),
            ]);
    }
}