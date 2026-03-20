<?php

namespace App\Filament\Resources\Marketing\MarketingPemesanans;

use App\Filament\Resources\Marketing\MarketingPemesanans\Pages\CreateMarketingPemesanan;
use App\Filament\Resources\Marketing\MarketingPemesanans\Pages\EditMarketingPemesanan;
use App\Filament\Resources\Marketing\MarketingPemesanans\Pages\ListMarketingPemesanans;
use App\Filament\Resources\Marketing\MarketingPemesanans\Schemas\MarketingPemesananForm;
use App\Filament\Resources\Marketing\MarketingPemesanans\Tables\MarketingPemesanansTable;
use App\Models\Pesanan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MarketingPemesananResource extends Resource
{
    protected static ?string $model = Pesanan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'pesanan';
    protected static ?string $slug = 'pesanan'; 

    public static function form(Schema $schema): Schema
    {
        return MarketingPemesananForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MarketingPemesanansTable::configure($table);
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
        return 'heroicon-o-rectangle-stack'; // bisa diganti icon lain
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMarketingPemesanans::route('/'),
            // 'create' => CreateMarketingPemesanan::route('/create'),
            // 'edit' => EditMarketingPemesanan::route('/{record}/edit'),
        ];
    }
}
