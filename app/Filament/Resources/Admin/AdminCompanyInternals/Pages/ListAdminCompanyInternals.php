<?php

namespace App\Filament\Resources\Admin\AdminCompanyInternals\Pages;

use App\Filament\Resources\Admin\AdminCompanyInternals\AdminCompanyInternalResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdminCompanyInternals extends ListRecords
{
    protected static string $resource = AdminCompanyInternalResource::class;
    protected static ?string $title = "Daftar Perusahaan Internal";

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Tambahkan Perusahaan Internal'),
        ];
    }
}
