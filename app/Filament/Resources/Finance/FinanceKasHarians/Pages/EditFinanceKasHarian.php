<?php

namespace App\Filament\Resources\Finance\FinanceKasHarians\Pages;

use App\Filament\Resources\Finance\FinanceKasHarians\FinanceKasHarianResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFinanceKasHarian extends EditRecord
{
    protected static string $resource = FinanceKasHarianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
