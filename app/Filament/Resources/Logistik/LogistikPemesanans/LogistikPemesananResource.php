<?php

namespace App\Filament\Resources\Logistik\LogistikPemesanans;

use App\Filament\Resources\Logistik\LogistikPemesanans\Pages\CreateLogistikPemesanan;
use App\Filament\Resources\Logistik\LogistikPemesanans\Pages\EditLogistikPemesanan;
use App\Filament\Resources\Logistik\LogistikPemesanans\Pages\ListLogistikPemesanans;
use App\Filament\Resources\Logistik\LogistikPemesanans\Schemas\LogistikPemesananForm;
use App\Filament\Resources\Logistik\LogistikPemesanans\Tables\LogistikPemesanansTable;
use App\Models\Pesanan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LogistikPemesananResource extends Resource
{
    protected static ?string $model = Pesanan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'pesanan';
    protected static ?string $slug = 'pesanan'; 

    public static function form(Schema $schema): Schema
    {
        return LogistikPemesananForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LogistikPemesanansTable::configure($table);
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
            'index' => ListLogistikPemesanans::route('/'),
            // 'create' => CreateLogistikPemesanan::route('/create'),
            // 'edit' => EditLogistikPemesanan::route('/{record}/edit'),
        ];
    }
}
