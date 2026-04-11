<?php

namespace App\Filament\Resources\Admin\AdminCompanyInternals\Pages;

use App\Filament\Resources\Admin\AdminCompanyInternals\AdminCompanyInternalResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAdminCompanyInternal extends EditRecord
{
    protected static string $resource = AdminCompanyInternalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
