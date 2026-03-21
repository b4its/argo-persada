<?php

namespace App\Filament\Resources\Finance\FinancePemesanans;

use App\Filament\Resources\Finance\FinancePemesanans\Pages\CreateFinancePemesanan;
use App\Filament\Resources\Finance\FinancePemesanans\Pages\EditFinancePemesanan;
use App\Filament\Resources\Finance\FinancePemesanans\Pages\ListFinancePemesanans;
use App\Filament\Resources\Finance\FinancePemesanans\Schemas\FinancePemesananForm;
use App\Filament\Resources\Finance\FinancePemesanans\Tables\FinancePemesanansTable;
use App\Models\FinancePemesanan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FinancePemesananResource extends Resource
{
    protected static ?string $model = FinancePemesanan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'pesanan';

    public static function form(Schema $schema): Schema
    {
        return FinancePemesananForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FinancePemesanansTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFinancePemesanans::route('/'),
            'create' => CreateFinancePemesanan::route('/create'),
            'edit' => EditFinancePemesanan::route('/{record}/edit'),
        ];
    }
}
