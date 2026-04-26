<?php

namespace App\Filament\Widgets\Admin;

use App\Models\KasHarian;
use App\Models\Pesanan;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $dataTerkini = KasHarian::latest()->first(); 
        $saldoTerkini = $dataTerkini ? $dataTerkini->saldo_akhir : 0;
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
        
        $totalPesananMingguIni = Pesanan::whereBetween('created_at', [
            Carbon::now()->startOfWeek(), 
            Carbon::now()->endOfWeek()
        ])
        ->count();
        $totalPesananKeseluruhan = Pesanan::count();
        $totalPesananRilisDanaDiterima = Pesanan::where('status_perilisan_dana', 3)->count();
        $totalPesananDtiolakRilisDana = Pesanan::where('status_perilisan_dana', 2)->count();
        $totalPesananRilisDana = Pesanan::where('status_perilisan_dana', 0)->count();

        $totalAkunMarketing = User::where('role', 'marketing')->count();
        $totalAkunFinance = User::where('role', 'finance')->count();
        $totalAkunLogistik = User::where('role', 'logistik')->count();
        return [
            //
            Stat::make('Akun Marketing', $totalAkunMarketing)
                ->description('Total Akun Marketing')
                ->descriptionIcon('heroicon-o-user')
                ->color('info'),
            Stat::make('Akun Finance', $totalAkunFinance)
                ->description('Total Akun Finance')
                ->descriptionIcon('heroicon-o-user')
                ->color('success'),
            Stat::make('Akun Logistik', $totalAkunLogistik)
                ->description('Total Akun Logistik')
                ->descriptionIcon('heroicon-o-user')
                ->color('lavender'),

            Stat::make('Pesanan', $totalPesananKeseluruhan)
                ->description('Total Pesanan')
                ->descriptionIcon('heroicon-o-shopping-bag')
                ->color('royal'),            

            Stat::make('Pesanan Minggu Ini', $totalPesananMingguIni)
                ->description('Total Pesanan Minggu Ini')
                ->descriptionIcon('heroicon-o-shopping-bag')
                ->color('info'),
                
            Stat::make('Pesanan Perlu diproses Rilis Dana', $totalPesananRilisDana)
                ->description('Total Pesanan perlu diproses Rilis Dana')
                ->descriptionIcon('heroicon-o-shopping-bag')
                ->color('ocean'),
                
            Stat::make('Pesanan Rilis Dana Diterima', $totalPesananRilisDanaDiterima)
                ->description('Total Pesanan Rilis Dana Diterima')
                ->descriptionIcon('heroicon-o-shopping-bag')
                ->color('success'),

            Stat::make('Pesanan Rilis Dana Ditolak', $totalPesananDtiolakRilisDana)
                ->description('Total Pesanan Rilis Dana Ditolak')
                ->descriptionIcon('heroicon-o-shopping-bag')
                ->color('danger'),

            Stat::make('Saldo Terkini', 'Rp '.number_format($saldoTerkini, 0, ',', '.'))
                ->description('Total Saldo Dimiliki')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Pemasukan Minggu Ini', 'Rp ' . number_format($totalDebetMingguIni, 0, ',', '.'))
                ->description('Total pemasukan/debet pada minnggu ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up') // Icon tren turun karena uang keluar
                ->color('success'),

            Stat::make('Pengeluaran Minggu Ini', 'Rp ' . number_format($totalKreditMingguIni, 0, ',', '.'))
                ->description('Total pengeluaran/kredit pada minnggu ini')
                ->descriptionIcon('heroicon-m-arrow-trending-down') // Icon tren turun karena uang keluar
                ->color('danger'),

        ];
    }
}
