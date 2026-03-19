<?php

namespace App\Filament\Resources\Marketing\MarketingPemesanans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MarketingPemesanansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Pembuat (User)')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('code')
                    ->label('Kode Pesanan')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('group_name')
                    ->label('Nama Grup')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('company_name')
                    ->label('Nama Perusahaan')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('keranjang.sub_total')
                    ->label('Total Barang')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                // Tambahkan filter di sini jika diperlukan
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}