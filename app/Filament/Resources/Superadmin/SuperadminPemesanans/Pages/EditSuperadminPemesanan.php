<?php

namespace App\Filament\Resources\Superadmin\SuperadminPemesanans\Pages;

use App\Filament\Resources\Superadmin\SuperadminPemesanans\SuperadminPemesananResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSuperadminPemesanan extends EditRecord
{
    protected static string $resource = SuperadminPemesananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
