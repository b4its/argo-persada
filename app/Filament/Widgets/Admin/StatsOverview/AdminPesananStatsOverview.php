<?php

namespace App\Filament\Widgets\Admin\StatsOverview;

use App\Models\Pesanan;
use App\Filament\Traits\HasDateFilter;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminPesananStatsOverview extends StatsOverviewWidget
{
    use HasDateFilter;

    protected ?string $heading = 'Statistik Pesanan';
    protected function getStats(): array
    {
        $range = $this->getFilteredDateRange();

        if ($range) {
            $totalPesanan = Pesanan::whereBetween('created_at', $range)->count();
            $totalPesananRilisDanaDiterima = Pesanan::where('status_perilisan_dana', 3)
                ->whereBetween('created_at', $range)->count();
            $totalPesananDtiolakRilisDana = Pesanan::where('status_perilisan_dana', 2)
                ->whereBetween('created_at', $range)->count();
            $totalPesananRilisDana = Pesanan::where('status_perilisan_dana', 0)
                ->whereBetween('created_at', $range)->count();
        } else {
            $totalPesanan = Pesanan::count();
            $totalPesananRilisDanaDiterima = Pesanan::where('status_perilisan_dana', 3)->count();
            $totalPesananDtiolakRilisDana = Pesanan::where('status_perilisan_dana', 2)->count();
            $totalPesananRilisDana = Pesanan::where('status_perilisan_dana', 0)->count();
        }

        return [
            Stat::make('Pesanan', $totalPesanan)
                ->description('Total Pesanan')
                ->descriptionIcon('heroicon-o-shopping-bag')
                ->color('royal'),

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
