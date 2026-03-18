<?php

namespace App\Filament\Resources\Admin\AdminKaryawans\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AdminKaryawanForm
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
                        'marketing' => 'Marketing',
                        'finance' => 'Finance',
                        'logistik' => 'Logistik',
                    ])
                    ->required(),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn ($state) => filled($state)),
            ]);
    }
}
