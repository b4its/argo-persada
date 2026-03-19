<?php

namespace App\Filament\Resources\Superadmin\Akuns\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AkunForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                TextInput::make('name')
                    ->label('Nama')
                    ->required(),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required(),
                Select::make('role')
                    ->label('Role')
                    ->options([
                        'admin' => 'Admin',
                        'marketing' => 'Marketing',
                        'finance' => 'Finance',
                        'logistik' => 'Logistik',
                    ])
                    ->required(),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required(),
            ]);
    }
}
