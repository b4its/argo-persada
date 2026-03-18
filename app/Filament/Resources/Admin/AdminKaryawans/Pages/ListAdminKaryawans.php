<?php

namespace App\Filament\Resources\Admin\AdminKaryawans\Pages;

use App\Filament\Resources\Admin\AdminKaryawans\AdminKaryawanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdminKaryawans extends ListRecords
{
    protected static ?string $title = 'Karyawan';
    protected static string $resource = AdminKaryawanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
