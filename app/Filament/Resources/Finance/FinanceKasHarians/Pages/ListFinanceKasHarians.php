<?php

namespace App\Filament\Resources\Finance\FinanceKasHarians\Pages;

use App\Filament\Resources\Finance\FinanceKasHarians\FinanceKasHarianResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFinanceKasHarians extends ListRecords
{
    protected static ?string $title = "Daftar Kas Harian";
    protected static string $resource = FinanceKasHarianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label("Tambahkan Kas Harian"),
            Action::make('cetak_kas_harian')
                        ->label('Cetak Keseluruhan Kas Harian')
                        ->icon('heroicon-o-document-text')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Cetak Kas Harian')
                        ->modalDescription('Apakah anda ingin mencetak dokumen kas harian ini?')
                        ->modalSubmitActionLabel('Ya, Cetak')
                        ->url(route('kas_harian_all.index'))
                        ->openUrlInNewTab(),
        ];
    }
}
