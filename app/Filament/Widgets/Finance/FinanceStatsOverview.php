<?php

namespace App\Filament\Widgets\Finance;

use App\Models\KasHarian;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinanceStatsOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Statistik Transaksi';
    protected function getStats(): array
    {
        $dataTerkini = KasHarian::latest()->first(); 
        $saldoTerkini = $dataTerkini ? $dataTerkini->saldo_akhir : 0;
        $totalKreditBulanIni = KasHarian::whereBetween('created_at', [
            Carbon::now()->startOfMonth(), 
            Carbon::now()->endOfMonth()
        ])
        ->sum('kredit');
        $totalDebetBulanIni = KasHarian::whereBetween('created_at', [
            Carbon::now()->startOfMonth(), 
            Carbon::now()->endOfMonth()
        ])
        ->sum('debet');
        $totalTransaksiBulanIni = KasHarian::whereBetween('created_at', [
            Carbon::now()->startOfMonth(), 
            Carbon::now()->endOfMonth()
        ])
        ->count();
        $totalKreditMingguIni = KasHarian::whereBetween('created_at', [
            Carbon::now()->startOfWeek(), 
            Carbon::now()->endOfWeek()
        ])
        ->sum('kredit');
        $totalDebetMingguIni = KasHarian::whereBetween('created_at', [
            Carbon::now()->startOfWeek(), 
            Carbon::now()->endOfWeek()
        ])
        ->sum('debet');
        $totalTransaksiMingguIni = KasHarian::whereBetween('created_at', [
            Carbon::now()->startOfWeek(), 
            Carbon::now()->endOfWeek()
        ])
        ->count();
        $totalTransaksiHariIni = KasHarian::whereBetween('created_at', [
                now()->startOfDay(), 
                now()->endOfDay()
            ])->count();
                    
        $totalDebetHariIni = KasHarian::whereBetween('created_at', [
                now()->startOfDay(), 
                now()->endOfDay()
            ])->sum('debet');
        
        return [
            //
            Stat::make('Transaksi Bulan Ini', $totalTransaksiBulanIni)
                ->description('Total Transaksi Bulan Ini')
                ->descriptionIcon('heroicon-m-banknotes') // Icon tren turun karena uang keluar
                ->color('ocean'),
                
            Stat::make('Pemasukan Bulan Ini', 'Rp ' . number_format($totalDebetBulanIni, 0, ',', '.'))
                ->description('Total pemasukan/debet pada minggu ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up') // Icon tren turun karena uang keluar
                ->color('success'),

            Stat::make('Pengeluaran Bulan Ini', 'Rp ' . number_format($totalKreditBulanIni, 0, ',', '.'))
                ->description('Total pengeluaran/kredit pada minggu ini')
                ->descriptionIcon('heroicon-m-arrow-trending-down') // Icon tren turun karena uang keluar
                ->color('danger'),

                Stat::make('Transaksi Minggu Ini', $totalTransaksiMingguIni)
                ->description('Total Transaksi Minggu Ini')
                ->descriptionIcon('heroicon-m-banknotes') // Icon tren turun karena uang keluar
                ->color('ocean'),
                
            Stat::make('Pemasukan Minggu Ini', 'Rp ' . number_format($totalDebetMingguIni, 0, ',', '.'))
                ->description('Total pemasukan/debet pada minggu ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up') // Icon tren turun karena uang keluar
                ->color('success'),

            Stat::make('Pengeluaran Minggu Ini', 'Rp ' . number_format($totalKreditMingguIni, 0, ',', '.'))
                ->description('Total pengeluaran/kredit pada minggu ini')
                ->descriptionIcon('heroicon-m-arrow-trending-down') // Icon tren turun karena uang keluar
                ->color('danger'),
            
            Stat::make('Saldo Terkini', 'Rp '.number_format($saldoTerkini, 0, ',', '.'))
                ->description('Total Saldo Dimiliki')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
                
            Stat::make('Transaksi Hari Ini', $totalTransaksiHariIni)
                ->description('Total Transaksi Hari Ini')
                ->descriptionIcon('heroicon-m-banknotes') // Icon tren turun karena uang keluar
                ->color('ocean'),
            ];
            }
}
