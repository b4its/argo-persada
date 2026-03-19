<?php

namespace App\Filament\Resources\Superadmin\SuperadminPemesanans;

use App\Filament\Resources\Superadmin\SuperadminPemesanans\Pages\CreateSuperadminPemesanan;
use App\Filament\Resources\Superadmin\SuperadminPemesanans\Pages\EditSuperadminPemesanan;
use App\Filament\Resources\Superadmin\SuperadminPemesanans\Pages\ListSuperadminPemesanans;
use App\Filament\Resources\Superadmin\SuperadminPemesanans\Schemas\SuperadminPemesananForm;
use App\Filament\Resources\Superadmin\SuperadminPemesanans\Tables\SuperadminPemesanansTable;
use App\Models\SuperadminPemesanan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SuperadminPemesananResource extends Resource
{
    protected static ?string $model = SuperadminPemesanan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Pesanan';

    public static function form(Schema $schema): Schema
    {
        return SuperadminPemesananForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SuperadminPemesanansTable::configure($table);
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
            'index' => ListSuperadminPemesanans::route('/'),
            'create' => CreateSuperadminPemesanan::route('/create'),
            'edit' => EditSuperadminPemesanan::route('/{record}/edit'),
        ];
    }
}
