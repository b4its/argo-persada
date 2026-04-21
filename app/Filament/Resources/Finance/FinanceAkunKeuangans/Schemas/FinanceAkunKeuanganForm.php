<?php

namespace App\Filament\Resources\Finance\FinanceAkunKeuangans\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FinanceAkunKeuanganForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
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
            ]);

            
    }
}
