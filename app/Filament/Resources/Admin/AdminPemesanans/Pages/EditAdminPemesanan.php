<?php

namespace App\Filament\Resources\Admin\AdminPemesanans\Pages;

use App\Filament\Resources\Admin\AdminPemesanans\AdminPemesananResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAdminPemesanan extends EditRecord
{
    protected static string $resource = AdminPemesananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
