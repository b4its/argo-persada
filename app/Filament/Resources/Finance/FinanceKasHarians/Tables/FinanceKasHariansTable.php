<?php

namespace App\Filament\Resources\Finance\FinanceKasHarians\Tables;

use App\Models\KasHarian;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FinanceKasHariansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                KasHarian::query()
                    ->orderBy('created_at', 'desc')
            )
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('companyInternal.singkatan')
                    ->label('PT')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('pesanan.no_requisition')
                    ->label('PT')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
