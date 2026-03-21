<?php

namespace App\Filament\Resources\Finance\FinancePemesanans\Pages;

use App\Filament\Resources\Finance\FinancePemesanans\FinancePemesananResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFinancePemesanan extends EditRecord
{
    protected static string $resource = FinancePemesananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
