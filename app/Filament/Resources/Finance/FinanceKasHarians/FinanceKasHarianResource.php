<?php

namespace App\Filament\Resources\Finance\FinanceKasHarians;

use App\Filament\Resources\Finance\FinanceKasHarians\Pages\CreateFinanceKasHarian;
use App\Filament\Resources\Finance\FinanceKasHarians\Pages\EditFinanceKasHarian;
use App\Filament\Resources\Finance\FinanceKasHarians\Pages\ListFinanceKasHarians;
use App\Filament\Resources\Finance\FinanceKasHarians\Schemas\FinanceKasHarianForm;
use App\Filament\Resources\Finance\FinanceKasHarians\Tables\FinanceKasHariansTable;
use App\Models\KasHarian;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FinanceKasHarianResource extends Resource
{
    protected static ?string $model = KasHarian::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'kas_harian';
    protected static ?string $slug = 'kas-harian';

    public static function form(Schema $schema): Schema
    {
        return FinanceKasHarianForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FinanceKasHariansTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }


    public static function getNavigationLabel(): string
    {
        return 'Kas Harian';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-banknotes'; // bisa diganti icon lain
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFinanceKasHarians::route('/'),
            // 'create' => CreateFinanceKasHarian::route('/create'),
            // 'edit' => EditFinanceKasHarian::route('/{record}/edit'),
        ];
    }
}
