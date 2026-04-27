<?php

namespace App\Filament\Widgets\Logistik\StatsOverview;

use App\Models\Pesanan;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LogistikSuratJalanStatsOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Statistik Surat Jalan';
    protected function getStats(): array
    {
        $totalPesanan_sudah_terbit_surat_jalan_keseluruhan = Pesanan::whereBetween('created_at', [
            Carbon::now()->startOfMonth(), 
            Carbon::now()->endOfMonth()
        ])
        ->whereNotNull('tanggal_terbit_surat_jalan')->count();
        
        $totalPesanan_belum_terbit_surat_jalan_keseluruhan = Pesanan::whereBetween('created_at', [
            Carbon::now()->startOfMonth(), 
            Carbon::now()->endOfMonth()
        ])
        ->where('tanggal_terbit_surat_jalan', null)->count();
        $totalPesanan_sudah_terbit_surat_jalan_bulan_ini = Pesanan::whereBetween('created_at', [
            Carbon::now()->startOfMonth(), 
            Carbon::now()->endOfMonth()
        ])
        ->whereNotNull('tanggal_terbit_surat_jalan')->count();
        
        $totalPesanan_belum_terbit_surat_jalan_bulan_ini = Pesanan::whereBetween('created_at', [
            Carbon::now()->startOfMonth(), 
            Carbon::now()->endOfMonth()
        ])
        ->where('tanggal_terbit_surat_jalan', null)->count();
        $totalPesanan_sudah_terbit_surat_jalan_minggu_ini = Pesanan::whereBetween('created_at', [
            Carbon::now()->startOfWeek(), 
            Carbon::now()->endOfWeek()
        ])
        ->whereNotNull('tanggal_terbit_surat_jalan')->count();

        $totalPesanan_belum_terbit_surat_jalan_minggu_ini = Pesanan::whereBetween('created_at', [
            Carbon::now()->startOfWeek(), 
            Carbon::now()->endOfWeek()
        ])
        ->where('tanggal_terbit_surat_jalan', null)->count();
        

        return [
            //
            Stat::make('Pesanan Sudah Cetak Surat Jalan Keseluruhan', $totalPesanan_sudah_terbit_surat_jalan_keseluruhan)
                ->description('Total pesanan sudah cetak surat jalan keseluruhan')
                ->descriptionIcon('heroicon-m-arrow-trending-up') // Icon tren turun karena uang keluar
                ->color('success'),

            Stat::make('Pesanan Belum Cetak Surat Jalan Keseluruhan', $totalPesanan_belum_terbit_surat_jalan_keseluruhan)
                ->description('Total pesanan belum cetak surat jalan keseluruhan')
                ->descriptionIcon('heroicon-m-arrow-trending-down') // Icon tren turun karena uang keluar
                ->color('danger'),
            //
            Stat::make('Pesanan Sudah Cetak Surat Jalan di Bulan Ini', $totalPesanan_sudah_terbit_surat_jalan_bulan_ini)
                ->description('Total pesanan sudah cetak surat jalan di bulan ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up') // Icon tren turun karena uang keluar
                ->color('success'),

            Stat::make('Pesanan Belum Cetak Surat Jalan di Bulan Ini', $totalPesanan_belum_terbit_surat_jalan_bulan_ini)
                ->description('Total pesanan belum cetak surat jalan di bulan ini')
                ->descriptionIcon('heroicon-m-arrow-trending-down') // Icon tren turun karena uang keluar
                ->color('danger'),
            //
            Stat::make('Pesanan Sudah Cetak Surat Jalan di Minggu Ini', $totalPesanan_sudah_terbit_surat_jalan_minggu_ini)
                ->description('Total pesanan sudah cetak surat jalan di minggu ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up') // Icon tren turun karena uang keluar
                ->color('success'),

            Stat::make('Pesanan Belum Cetak Surat Jalan di Minggu Ini', $totalPesanan_belum_terbit_surat_jalan_minggu_ini)
                ->description('Total pesanan belum cetak surat jalan di minggu ini')
                ->descriptionIcon('heroicon-m-arrow-trending-down') // Icon tren turun karena uang keluar
                ->color('danger'),
        ];
    }
}
