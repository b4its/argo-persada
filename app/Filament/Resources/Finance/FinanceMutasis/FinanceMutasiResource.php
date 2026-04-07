<?php

namespace App\Filament\Resources\Finance\FinanceMutasis;

use App\Filament\Resources\Finance\FinanceMutasis\Pages\CreateFinanceMutasi;
use App\Filament\Resources\Finance\FinanceMutasis\Pages\EditFinanceMutasi;
use App\Filament\Resources\Finance\FinanceMutasis\Pages\ListFinanceMutasis;
use App\Filament\Resources\Finance\FinanceMutasis\Schemas\FinanceMutasiForm;
use App\Filament\Resources\Finance\FinanceMutasis\Tables\FinanceMutasisTable;
use App\Models\BukuBesar;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FinanceMutasiResource extends Resource
{
    protected static ?string $model = BukuBesar::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'buku_besar';
    protected static ?string $slug = 'buku-besar-mutasi';

    public static function form(Schema $schema): Schema
    {
        return FinanceMutasiForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FinanceMutasisTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationGroup(): string
    {
        return 'Buku Besar';
    }

    public static function getNavigationLabel(): string
    {
        return 'Mutasi';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-banknotes'; // bisa diganti icon lain
    }


    public static function getPages(): array
    {
        return [
            'index' => ListFinanceMutasis::route('/'),
            // 'create' => CreateFinanceMutasi::route('/create'),
            // 'edit' => EditFinanceMutasi::route('/{record}/edit'),
        ];
    }
}
