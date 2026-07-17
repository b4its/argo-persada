<?php

namespace App\Filament\Resources\Logistik\LogistikPemesanans\Pages;

use App\Filament\Resources\Logistik\LogistikPemesanans\LogistikPemesananResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLogistikPemesanans extends ListRecords
{
    protected static ?string $title = "Daftar Pemesanan";
    protected static string $resource = LogistikPemesananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
}
