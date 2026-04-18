<?php

namespace App\Filament\Resources\Finance\FinanceSaldos\Pages;

use App\Filament\Resources\Finance\FinanceSaldos\FinanceSaldoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFinanceSaldos extends ListRecords
{
    protected static ?string $title = "Daftar Saldo";
    protected static string $resource = FinanceSaldoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label("Tambahkan Saldo"),
        ];
    }
}
