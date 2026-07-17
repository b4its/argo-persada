<?php

namespace App\Filament\Widgets\Admin\StatsOverview;

use App\Models\KasHarian;
use App\Filament\Traits\HasDateFilter;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminKasHarianStatsOverview extends StatsOverviewWidget
{
    use HasDateFilter;

    protected ?string $heading = 'Statistik Transaksi';
    protected function getStats(): array
    {
        $range = $this->getFilteredDateRange();

        $dataTerkini = KasHarian::latest()->first();
        $saldoTerkini = $dataTerkini ? $dataTerkini->saldo_akhir : 0;

        if ($range) {
            $totalKredit = KasHarian::whereBetween('created_at', $range)->sum('kredit');
            $totalDebet = KasHarian::whereBetween('created_at', $range)->sum('debet');
            $totalTransaksi = KasHarian::whereBetween('created_at', $range)->count();
        } else {
            $totalKreditBulanIni = KasHarian::whereBetween('created_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])->sum('kredit');
            $totalDebetBulanIni = KasHarian::whereBetween('created_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])->sum('debet');
            $totalTransaksiBulanIni = KasHarian::whereBetween('created_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])->count();
            $totalKreditMingguIni = KasHarian::whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])->sum('kredit');
            $totalDebetMingguIni = KasHarian::whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])->sum('debet');
            $totalTransaksiMingguIni = KasHarian::whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])->count();
            $totalTransaksiHariIni = KasHarian::whereBetween('created_at', [
                now()->startOfDay(),
                now()->endOfDay()
            ])->count();
            $totalDebetHariIni = KasHarian::whereBetween('created_at', [
                now()->startOfDay(),
                now()->endOfDay()
            ])->sum('debet');

            return [
                Stat::make('Transaksi Bulan Ini', $totalTransaksiBulanIni)
                    ->description('Total Transaksi Bulan Ini')
                    ->descriptionIcon('heroicon-m-banknotes')
                    ->color('ocean'),

                Stat::make('Pemasukan Bulan Ini', 'Rp ' . number_format($totalDebetBulanIni, 0, ',', '.'))
                    ->description('Total pemasukan/debet pada bulan ini')
                    ->descriptionIcon('heroicon-m-arrow-trending-up')
                    ->color('success'),

                Stat::make('Pengeluaran Bulan Ini', 'Rp ' . number_format($totalKreditBulanIni, 0, ',', '.'))
                    ->description('Total pengeluaran/kredit pada bulan ini')
                    ->descriptionIcon('heroicon-m-arrow-trending-down')
                    ->color('danger'),

                Stat::make('Transaksi Minggu Ini', $totalTransaksiMingguIni)
                    ->description('Total Transaksi Minggu Ini')
                    ->descriptionIcon('heroicon-m-banknotes')
                    ->color('ocean'),

                Stat::make('Pemasukan Minggu Ini', 'Rp ' . number_format($totalDebetMingguIni, 0, ',', '.'))
                    ->description('Total pemasukan/debet pada minggu ini')
                    ->descriptionIcon('heroicon-m-arrow-trending-up')
                    ->color('success'),

                Stat::make('Pengeluaran Minggu Ini', 'Rp ' . number_format($totalKreditMingguIni, 0, ',', '.'))
                    ->description('Total pengeluaran/kredit pada minggu ini')
                    ->descriptionIcon('heroicon-m-arrow-trending-down')
                    ->color('danger'),

                Stat::make('Saldo Terkini', 'Rp ' . number_format($saldoTerkini, 0, ',', '.'))
                    ->description('Total Saldo Dimiliki')
                    ->descriptionIcon('heroicon-m-banknotes')
                    ->color('success'),

                Stat::make('Transaksi Hari Ini', $totalTransaksiHariIni)
                    ->description('Total Transaksi Hari Ini')
                    ->descriptionIcon('heroicon-m-banknotes')
                    ->color('ocean'),
            ];
        }

        return [
            Stat::make('Transaksi', $totalTransaksi)
                ->description('Total Transaksi')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('ocean'),

            Stat::make('Pemasukan', 'Rp ' . number_format($totalDebet, 0, ',', '.'))
                ->description('Total pemasukan/debet')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Pengeluaran', 'Rp ' . number_format($totalKredit, 0, ',', '.'))
                ->description('Total pengeluaran/kredit')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),

            Stat::make('Saldo Terkini', 'Rp ' . number_format($saldoTerkini, 0, ',', '.'))
                ->description('Total Saldo Dimiliki')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
