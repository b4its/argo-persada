<?php

namespace App\Filament\Resources\Finance\FinanceKasHarians\Pages;

use App\Filament\Resources\Finance\FinanceKasHarians\FinanceKasHarianResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListFinanceKasHarians extends ListRecords
{
    protected static ?string $title = "Daftar Kas Harian";
    protected static string $resource = FinanceKasHarianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label("Tambahkan Kas Harian")
                ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = Auth::user()->id;
                        return $data;
                    }),
            // 1. Action Cetak Kas Harian
            Action::make('cetak_kas_harian')
                ->label('Cetak Kas Harian')
                ->icon('heroicon-o-printer')
                ->color('warning')
                ->modalHeading('Cetak Laporan Kas Harian')
                ->modalDescription('Silakan pilih rentang waktu laporan yang ingin dicetak.')
                ->form([
                    Select::make('filter_type')
                        ->label('Periode Waktu')
                        ->options([
                            'all' => 'Keseluruhan',
                            'year' => 'Per Tahun',
                            'month' => 'Per Bulan',
                            'week' => 'Per Minggu',
                            'day' => 'Per Hari',
                            'custom' => 'Custom Tanggal',
                        ])
                        ->default('all')
                        ->reactive(),
                    Select::make('year')
                        ->label('Tahun')
                        ->options(array_combine(range(date('Y') - 5, date('Y')), range(date('Y') - 5, date('Y'))))
                        ->visible(fn ($get) => in_array($get('filter_type'), ['year', 'month'])),
                    Select::make('month')
                        ->label('Bulan')
                        ->options([
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
                            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ])
                        ->visible(fn ($get) => $get('filter_type') === 'month'),
                    DatePicker::make('date')
                        ->label('Pilih Tanggal Acuan')
                        ->visible(fn ($get) => in_array($get('filter_type'), ['day', 'week'])),
                    DatePicker::make('start_date')
                        ->label('Dari Tanggal')
                        ->visible(fn ($get) => $get('filter_type') === 'custom'),
                    DatePicker::make('end_date')
                        ->label('Sampai Tanggal')
                        ->visible(fn ($get) => $get('filter_type') === 'custom'),
                ])
                ->action(function (array $data) {
                    // Redirect ke route dengan membawa parameter filter
                    return redirect()->route('kas_harian_all.index', $data);
                }),

            // 2. Action Cetak Buku Besar
            Action::make('cetak_buku_besar')
                ->label('Cetak Buku Besar')
                ->icon('heroicon-o-book-open')
                ->color('success')
                ->modalHeading('Cetak Buku Besar')
                ->modalDescription('Silakan pilih rentang waktu buku besar yang ingin dicetak.')
                ->form([
                    Select::make('filter_type')
                        ->label('Periode Waktu')
                        ->options([
                            'all' => 'Keseluruhan',
                            'year' => 'Per Tahun',
                            'month' => 'Per Bulan',
                            'week' => 'Per Minggu',
                            'day' => 'Per Hari',
                            'custom' => 'Custom Tanggal',
                        ])
                        ->default('all')
                        ->reactive(),
                    Select::make('year')
                        ->label('Tahun')
                        ->options(array_combine(range(date('Y') - 5, date('Y')), range(date('Y') - 5, date('Y'))))
                        ->visible(fn ($get) => in_array($get('filter_type'), ['year', 'month'])),
                    Select::make('month')
                        ->label('Bulan')
                        ->options([
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
                            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ])
                        ->visible(fn ($get) => $get('filter_type') === 'month'),
                    DatePicker::make('date')
                        ->label('Pilih Tanggal Acuan')
                        ->visible(fn ($get) => in_array($get('filter_type'), ['day', 'week'])),
                    DatePicker::make('start_date')
                        ->label('Dari Tanggal')
                        ->visible(fn ($get) => $get('filter_type') === 'custom'),
                    DatePicker::make('end_date')
                        ->label('Sampai Tanggal')
                        ->visible(fn ($get) => $get('filter_type') === 'custom'),
                ])
                ->action(function (array $data) {
                    return redirect()->route('buku_besar.index', $data);
                }),
        ];
    }
}
