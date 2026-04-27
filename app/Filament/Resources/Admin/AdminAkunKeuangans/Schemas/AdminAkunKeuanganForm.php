<?php

namespace App\Filament\Resources\Admin\AdminAkunKeuangans\Schemas;

use App\Filament\Resources\Finance\FinanceAkunKeuangans\Schemas\FinanceAkunKeuanganForm;
use Filament\Schemas\Schema;

class AdminAkunKeuanganForm
{
    public static function configure(Schema $schema): Schema
    {
        return FinanceAkunKeuanganForm::configure($schema);
    }
}
