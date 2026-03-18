<?php

namespace App\Filament\Resources\Superadmin\Akuns\Pages;

use App\Filament\Resources\Superadmin\Akuns\AkunResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAkun extends EditRecord
{
    protected static string $resource = AkunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
