<?php

namespace App\Filament\Resources\Admin\AdminSaldos\Pages;

use App\Filament\Resources\Admin\AdminSaldos\AdminSaldoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAdminSaldo extends EditRecord
{
    protected static string $resource = AdminSaldoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
