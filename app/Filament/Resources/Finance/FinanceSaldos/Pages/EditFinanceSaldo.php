<?php

namespace App\Filament\Resources\Finance\FinanceSaldos\Pages;

use App\Filament\Resources\Finance\FinanceSaldos\FinanceSaldoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFinanceSaldo extends EditRecord
{
    protected static string $resource = FinanceSaldoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
