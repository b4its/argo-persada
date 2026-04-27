<?php

namespace App\Filament\Resources\Admin\AdminKasHarians\Pages;

use App\Filament\Resources\Admin\AdminKasHarians\AdminKasHarianResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAdminKasHarian extends EditRecord
{
    protected static string $resource = AdminKasHarianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
