<?php

namespace App\Filament\Widgets\Admin\StatsOverview;

use App\Models\Pesanan;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminPesananStatsOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Statistik Pesanan';
    protected function getStats(): array
    {
        $totalPesananMingguIni = Pesanan::whereBetween('created_at', [
            Carbon::now()->startOfWeek(), 
            Carbon::now()->endOfWeek()
        ])
        ->count();
        $totalPesananKeseluruhan = Pesanan::count();
        $totalPesananRilisDanaDiterima = Pesanan::where('status_perilisan_dana', 3)->count();
        $totalPesananDtiolakRilisDana = Pesanan::where('status_perilisan_dana', 2)->count();
        $totalPesananRilisDana = Pesanan::where('status_perilisan_dana', 0)->count();
        return [
            //
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
        ];
    }
}
