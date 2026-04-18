<?php

namespace App\Filament\Resources\Admin\AdminSaldos\Pages;

use App\Filament\Resources\Admin\AdminSaldos\AdminSaldoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdminSaldos extends ListRecords
{
    protected static ?string $title = "Daftar Saldo";
    protected static string $resource = AdminSaldoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label("Tambahkan Saldo"),
        ];
    }
}
