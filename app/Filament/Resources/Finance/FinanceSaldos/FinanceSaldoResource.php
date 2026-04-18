<?php

namespace App\Filament\Resources\Finance\FinanceSaldos;

use App\Filament\Resources\Finance\FinanceSaldos\Pages\CreateFinanceSaldo;
use App\Filament\Resources\Finance\FinanceSaldos\Pages\EditFinanceSaldo;
use App\Filament\Resources\Finance\FinanceSaldos\Pages\ListFinanceSaldos;
use App\Filament\Resources\Finance\FinanceSaldos\Schemas\FinanceSaldoForm;
use App\Filament\Resources\Finance\FinanceSaldos\Tables\FinanceSaldosTable;
use App\Models\Saldo;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FinanceSaldoResource extends Resource
{
    protected static ?string $model = Saldo::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'saldo';
    protected static ?string $slug = 'saldo';

    public static function form(Schema $schema): Schema
    {
        return FinanceSaldoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FinanceSaldosTable::configure($table);
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
            'index' => ListFinanceSaldos::route('/'),
            // 'create' => CreateFinanceSaldo::route('/create'),
            // 'edit' => EditFinanceSaldo::route('/{record}/edit'),
        ];
    }
}
