<?php

namespace App\Filament\Resources\Superadmin\SuperadminPemesanans\Pages;

use App\Filament\Resources\Superadmin\SuperadminPemesanans\SuperadminPemesananResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSuperadminPemesanans extends ListRecords
{
    protected static string $resource = SuperadminPemesananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
