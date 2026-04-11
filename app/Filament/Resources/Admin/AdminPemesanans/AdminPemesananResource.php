<?php

namespace App\Filament\Resources\Admin\AdminPemesanans;

use App\Filament\Resources\Admin\AdminPemesanans\Pages\CreateAdminPemesanan;
use App\Filament\Resources\Admin\AdminPemesanans\Pages\EditAdminPemesanan;
use App\Filament\Resources\Admin\AdminPemesanans\Pages\ListAdminPemesanans;
use App\Filament\Resources\Admin\AdminPemesanans\Schemas\AdminPemesananForm;
use App\Filament\Resources\Admin\AdminPemesanans\Tables\AdminPemesanansTable;
use App\Models\Pesanan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AdminPemesananResource extends Resource
{
    protected static ?string $model = Pesanan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Pesanan';
    protected static ?string $slug = 'pesanan'; 

    public static function form(Schema $schema): Schema
    {
        return AdminPemesananForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdminPemesanansTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Pemesanan';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-shopping-bag'; // bisa diganti icon lain
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdminPemesanans::route('/'),
            // 'create' => CreateAdminPemesanan::route('/create'),
            // 'edit' => EditAdminPemesanan::route('/{record}/edit'),
        ];
    }
}
