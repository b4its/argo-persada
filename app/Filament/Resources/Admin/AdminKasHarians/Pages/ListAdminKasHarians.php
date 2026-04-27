<?php

namespace App\Filament\Resources\Admin\AdminKasHarians\Pages;

use App\Filament\Resources\Admin\AdminKasHarians\AdminKasHarianResource;
use App\Filament\Resources\Finance\FinanceKasHarians\Pages\ListFinanceKasHarians;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdminKasHarians extends ListFinanceKasHarians
{
    protected static string $resource = AdminKasHarianResource::class;

}
