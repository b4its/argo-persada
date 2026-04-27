<?php

namespace App\Filament\Widgets\Marketing;

use App\Models\Pesanan;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MarketingStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalPesanan_sudah_cetak_requisition_keseluruhan = Pesanan::whereBetween('created_at', [
            Carbon::now()->startOfMonth(), 
            Carbon::now()->endOfMonth()
        ])
        ->whereNotNull('no_requisition')->count();
        
        $totalPesanan_belum_cetak_requisition_keseluruhan = Pesanan::whereBetween('created_at', [
            Carbon::now()->startOfMonth(), 
            Carbon::now()->endOfMonth()
        ])
        ->where('no_requisition', null)->count();
        $totalPesanan_sudah_cetak_requisition_bulan_ini = Pesanan::whereBetween('created_at', [
            Carbon::now()->startOfMonth(), 
            Carbon::now()->endOfMonth()
        ])
        ->whereNotNull('no_requisition')->count();
        
        $totalPesanan_belum_cetak_requisition_bulan_ini = Pesanan::whereBetween('created_at', [
            Carbon::now()->startOfMonth(), 
            Carbon::now()->endOfMonth()
        ])
        ->where('no_requisition', null)->count();
        $totalPesanan_sudah_cetak_requisition_minggu_ini = Pesanan::whereBetween('created_at', [
            Carbon::now()->startOfWeek(), 
            Carbon::now()->endOfWeek()
        ])
        ->whereNotNull('no_requisition')->count();

        $totalPesanan_belum_cetak_requisition_minggu_ini = Pesanan::whereBetween('created_at', [
            Carbon::now()->startOfWeek(), 
            Carbon::now()->endOfWeek()
        ])
        ->where('no_requisition', null)->count();
        return [
            //
            Stat::make('Pesanan Sudah Cetak No Requistion Keseluruhan', $totalPesanan_sudah_cetak_requisition_keseluruhan)
                ->description('Total pesanan sudah cetak no requistion keseluruhan')
                ->descriptionIcon('heroicon-m-arrow-trending-up') // Icon tren turun karena uang keluar
                ->color('success'),

            Stat::make('Pesanan Belum Cetak No Requistion Keseluruhan', $totalPesanan_belum_cetak_requisition_keseluruhan)
                ->description('Total pesanan belum cetak no requistion keseluruhan')
                ->descriptionIcon('heroicon-m-arrow-trending-down') // Icon tren turun karena uang keluar
                ->color('danger'),
            //
            Stat::make('Pesanan Sudah Cetak No Requistion di Bulan Ini', $totalPesanan_sudah_cetak_requisition_bulan_ini)
                ->description('Total pesanan sudah cetak no requistion di bulan ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up') // Icon tren turun karena uang keluar
                ->color('success'),

            Stat::make('Pesanan Belum Cetak No Requistion di Bulan Ini', $totalPesanan_belum_cetak_requisition_bulan_ini)
                ->description('Total pesanan belum cetak no requistion di bulan ini')
                ->descriptionIcon('heroicon-m-arrow-trending-down') // Icon tren turun karena uang keluar
                ->color('danger'),
            //
            Stat::make('Pesanan Sudah Cetak No Requistion di Minggu Ini', $totalPesanan_sudah_cetak_requisition_minggu_ini)
                ->description('Total pesanan sudah cetak no requistion di minggu ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up') // Icon tren turun karena uang keluar
                ->color('success'),

            Stat::make('Pesanan Belum Cetak No Requistion di Minggu Ini', $totalPesanan_belum_cetak_requisition_minggu_ini)
                ->description('Total pesanan belum cetak no requistion di minggu ini')
                ->descriptionIcon('heroicon-m-arrow-trending-down') // Icon tren turun karena uang keluar
                ->color('danger'),

        ];
    }
}
