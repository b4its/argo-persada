<?php

namespace App\Filament\Resources\Admin\AdminKasHarians\Tables;

use App\Filament\Resources\Finance\FinanceKasHarians\Tables\FinanceKasHariansTable;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class AdminKasHariansTable
{
    public static function configure(Table $table): Table
    {
        return FinanceKasHariansTable::configure($table);
    }
}
