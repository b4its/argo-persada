<?php

namespace App\Filament\Resources\Admin\AdminSaldos;

use App\Filament\Resources\Admin\AdminSaldos\Pages\CreateAdminSaldo;
use App\Filament\Resources\Admin\AdminSaldos\Pages\EditAdminSaldo;
use App\Filament\Resources\Admin\AdminSaldos\Pages\ListAdminSaldos;
use App\Filament\Resources\Admin\AdminSaldos\Schemas\AdminSaldoForm;
use App\Filament\Resources\Admin\AdminSaldos\Tables\AdminSaldosTable;
use App\Models\Saldo;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AdminSaldoResource extends Resource
{
    protected static ?string $model = Saldo::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'saldo';
    protected static ?string $title = 'saldo';

    public static function form(Schema $schema): Schema
    {
        return AdminSaldoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdminSaldosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }


    public static function getNavigationLabel(): string
    {
        return 'Saldo';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-banknotes'; // bisa diganti icon lain
    }
    public static function getPages(): array
    {
        return [
            'index' => ListAdminSaldos::route('/'),
            // 'create' => CreateAdminSaldo::route('/create'),
            // 'edit' => EditAdminSaldo::route('/{record}/edit'),
        ];
    }
}
