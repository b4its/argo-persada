<?php

namespace App\Filament\Resources\Admin\AdminKasHarians\Schemas;

use App\Filament\Resources\Finance\FinanceKasHarians\Schemas\FinanceKasHarianForm;
use Filament\Schemas\Schema;

class AdminKasHarianForm
{
    public static function configure(Schema $schema): Schema
    {
        return FinanceKasHarianForm::configure($schema);
    }
}
