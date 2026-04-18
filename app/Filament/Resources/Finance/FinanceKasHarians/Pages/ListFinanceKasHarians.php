<?php

namespace App\Filament\Resources\Finance\FinanceKasHarians\Pages;

use App\Filament\Resources\Finance\FinanceKasHarians\FinanceKasHarianResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFinanceKasHarians extends ListRecords
{
    protected static ?string $title = "Daftar Kas Harian";
    protected static string $resource = FinanceKasHarianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
}
