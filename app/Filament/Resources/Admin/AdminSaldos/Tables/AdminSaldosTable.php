<?php

namespace App\Filament\Resources\Admin\AdminSaldos\Tables;

use App\Models\Saldo;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AdminSaldosTable
{
    public static function configure(Table $table): Table
    {
        return $table
           ->query(
                Saldo::query()
                    ->selectRaw('saldo.*, ROW_NUMBER() OVER (ORDER BY created_at desc) as row_num')
                    ->orderBy('created_at', 'desc') // urutkan tampilannya dari terbaru
            )
            ->columns([
                //
                TextColumn::make('row_num')
                    ->label('No')
                    ->sortable(),

                TextColumn::make('saldo_awal')
                    ->label('Saldo Awal')
                    ->money('IDR', locale: 'id_ID') // Mengatur mata uang ke Rupiah Indonesia
                    ->sortable()
                    ->searchable(),

                TextColumn::make('saldo_akhir')
                    ->label('Saldo Akhir')
                    ->money('IDR', locale: 'id_ID') // Mengatur mata uang ke Rupiah Indonesia
                    ->sortable()
                    ->searchable()

            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->button()
                    ->color('danger') // default abu-abu (tidak merah)
                    ->requiresConfirmation() // pastikan tampil popup konfirmasi
                    ->modalHeading('Konfirmasi Hapus')
                    ->modalDescription('apakah yakin ingin menghapus data ini?')
                    ->modalSubmitActionLabel('Ya, Hapus'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
