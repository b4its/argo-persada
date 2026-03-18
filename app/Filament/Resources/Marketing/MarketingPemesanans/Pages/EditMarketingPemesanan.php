<?php

namespace App\Filament\Resources\Marketing\MarketingPemesanans\Pages;

use App\Filament\Resources\Marketing\MarketingPemesanans\MarketingPemesananResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMarketingPemesanan extends EditRecord
{
    protected static string $resource = MarketingPemesananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
