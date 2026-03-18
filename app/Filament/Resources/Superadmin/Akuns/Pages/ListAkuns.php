<?php

namespace App\Filament\Resources\Superadmin\Akuns\Pages;

use App\Filament\Resources\Superadmin\Akuns\AkunResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAkuns extends ListRecords
{
    protected static string $resource = AkunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
