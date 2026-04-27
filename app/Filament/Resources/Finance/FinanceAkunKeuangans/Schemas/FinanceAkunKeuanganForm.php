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
            ]);

            
    }
}
