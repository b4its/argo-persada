<?php

namespace App\Filament\Resources\Finance\FinanceKasHarians\Tables;

use App\Models\KasHarian;
use Filament\Actions\Action;
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
                    ->label('PR/PO')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('cetak_kas_harian')
                        ->label('Cetak Kas Harian')
                        ->icon('heroicon-o-document-text')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Cetak Kas Harian')
                        ->modalDescription('Apakah anda ingin mencetak dokumen kas harian ini?')
                        ->modalSubmitActionLabel('Ya, Cetak')
                        ->url(fn ($record) => route('kas_harian.index', $record->id))
                        ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
