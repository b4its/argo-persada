<?php

namespace App\Filament\Resources\Admin\AdminSaldos\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class AdminSaldoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                TextInput::make('saldo_awal')
                    ->label('Saldo Awal')
                    ->prefix('Rp')
                    // Gunakan mask untuk tampilan ribuan yang cantik
                    ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                    ->stripCharacters('.')
                    ->required()
                    ->live(onBlur: true) // Gunakan onBlur agar tidak berat saat mengetik
                    ->columnSpanFull(),
            ]);
    }
}
