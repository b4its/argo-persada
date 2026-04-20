<?php

namespace App\Filament\Resources\Finance\FinanceAkunKeuangans;

use App\Filament\Resources\Finance\FinanceAkunKeuangans\Pages\CreateFinanceAkunKeuangan;
use App\Filament\Resources\Finance\FinanceAkunKeuangans\Pages\EditFinanceAkunKeuangan;
use App\Filament\Resources\Finance\FinanceAkunKeuangans\Pages\ListFinanceAkunKeuangans;
use App\Filament\Resources\Finance\FinanceAkunKeuangans\Schemas\FinanceAkunKeuanganForm;
use App\Filament\Resources\Finance\FinanceAkunKeuangans\Tables\FinanceAkunKeuangansTable;
use App\Models\FinanceAkunKeuangan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FinanceAkunKeuanganResource extends Resource
{
    protected static ?string $model = FinanceAkunKeuangan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'akun_keuangan';
    protected static ?string $slug = 'akun-keuangan';

    public static function form(Schema $schema): Schema
    {
        return FinanceAkunKeuanganForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FinanceAkunKeuangansTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Akun Keuangan';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-wallet'; // bisa diganti icon lain
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFinanceAkunKeuangans::route('/'),
            // 'create' => CreateFinanceAkunKeuangan::route('/create'),
            // 'edit' => EditFinanceAkunKeuangan::route('/{record}/edit'),
        ];
    }
}
