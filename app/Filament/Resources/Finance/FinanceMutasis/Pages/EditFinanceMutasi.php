<?php

namespace App\Filament\Resources\Finance\FinanceMutasis\Pages;

use App\Filament\Resources\Finance\FinanceMutasis\FinanceMutasiResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFinanceMutasi extends EditRecord
{
    protected static string $resource = FinanceMutasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
