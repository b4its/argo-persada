<?php

namespace App\Filament\Resources\Finance\FinanceAkunKeuangans\Pages;

use App\Filament\Resources\Finance\FinanceAkunKeuangans\FinanceAkunKeuanganResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFinanceAkunKeuangan extends EditRecord
{
    protected static string $resource = FinanceAkunKeuanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
