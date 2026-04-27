<?php

namespace App\Filament\Resources\Admin\AdminKasHarians;

use App\Filament\Resources\Admin\AdminKasHarians\Pages\CreateAdminKasHarian;
use App\Filament\Resources\Admin\AdminKasHarians\Pages\EditAdminKasHarian;
use App\Filament\Resources\Admin\AdminKasHarians\Pages\ListAdminKasHarians;
use App\Filament\Resources\Admin\AdminKasHarians\Schemas\AdminKasHarianForm;
use App\Filament\Resources\Admin\AdminKasHarians\Tables\AdminKasHariansTable;
use App\Models\KasHarian;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AdminKasHarianResource extends Resource
{
    protected static ?string $model = KasHarian::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'kas_harian';
    protected static ?string $slug = 'kas-harian';

    public static function form(Schema $schema): Schema
    {
        return AdminKasHarianForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdminKasHariansTable::configure($table);
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
            'index' => ListAdminKasHarians::route('/'),
            // 'create' => CreateAdminKasHarian::route('/create'),
            // 'edit' => EditAdminKasHarian::route('/{record}/edit'),
        ];
    }
}
