<?php

namespace App\Filament\Resources\Admin\AdminAkunKeuangans\Pages;

use App\Filament\Resources\Admin\AdminAkunKeuangans\AdminAkunKeuanganResource;
use App\Filament\Resources\Finance\FinanceAkunKeuangans\Pages\ListFinanceAkunKeuangans;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdminAkunKeuangans extends ListFinanceAkunKeuangans
{
    // WAJIB: Ganti resource-nya ke Admin agar rute dan Policy merujuk ke Admin
    protected static string $resource = AdminAkunKeuanganResource::class;

}
