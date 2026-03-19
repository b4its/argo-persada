<?php

namespace App\Filament\Resources\Admin\AdminPemesanans\Pages;

use App\Filament\Resources\Admin\AdminPemesanans\AdminPemesananResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdminPemesanans extends ListRecords
{
    protected static string $resource = AdminPemesananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
