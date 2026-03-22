<?php

namespace App\Filament\Resources\Finance\FinancePemesanans\Pages;

use App\Filament\Resources\Finance\FinancePemesanans\FinancePemesananResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFinancePemesanans extends ListRecords
{
    protected static ?string $title = 'Daftar Pemesanan';
    protected static string $resource = FinancePemesananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
}
