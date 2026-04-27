<?php

namespace App\Filament\Resources\Admin\AdminAkunKeuangans\Tables;

use App\Filament\Resources\Finance\FinanceAkunKeuangans\Tables\FinanceAkunKeuangansTable;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class AdminAkunKeuangansTable
{
    public static function configure(Table $table): Table
    {
        return FinanceAkunKeuangansTable::configure($table);
    }
}
