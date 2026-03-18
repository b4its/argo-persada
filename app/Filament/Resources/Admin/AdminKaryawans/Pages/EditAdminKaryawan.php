<?php

namespace App\Filament\Resources\Admin\AdminKaryawans\Pages;

use App\Filament\Resources\Admin\AdminKaryawans\AdminKaryawanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAdminKaryawan extends EditRecord
{
    protected static string $resource = AdminKaryawanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
