<?php

namespace App\Filament\Resources\Finance\FinanceAkunKeuangans\Pages;

use App\Filament\Resources\Finance\FinanceAkunKeuangans\FinanceAkunKeuanganResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFinanceAkunKeuangans extends ListRecords
{
    protected static string $resource = FinanceAkunKeuanganResource::class;
    protected static ?string $title = "Daftar Akun Keuangan";

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label("Tambahkan Akun Keuangan"),
        ];
    }
}
