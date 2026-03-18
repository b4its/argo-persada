<?php

namespace App\Filament\Resources\Marketing\MarketingPemesanans\Pages;

use App\Filament\Resources\Marketing\MarketingPemesanans\MarketingPemesananResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMarketingPemesanans extends ListRecords
{
    protected static string $resource = MarketingPemesananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
