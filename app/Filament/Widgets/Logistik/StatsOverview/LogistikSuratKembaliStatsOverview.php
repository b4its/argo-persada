<?php

namespace App\Filament\Widgets\Logistik\StatsOverview;

use App\Models\Pesanan;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LogistikSuratKembaliStatsOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Statistik Surat Kembali';
    protected function getStats(): array
    {
        $totalPesanan_sudah_surat_kembali_keseluruhan = Pesanan::whereBetween('created_at', [
            Carbon::now()->startOfMonth(), 
            Carbon::now()->endOfMonth()
        ])
        ->whereNotNull('tanggal_surat_kembali')->count();
        
        $totalPesanan_belum_surat_kembali_keseluruhan = Pesanan::whereBetween('created_at', [
            Carbon::now()->startOfMonth(), 
            Carbon::now()->endOfMonth()
        ])
        ->where('tanggal_surat_kembali', null)->count();
        $totalPesanan_sudah_surat_kembali_bulan_ini = Pesanan::whereBetween('created_at', [
            Carbon::now()->startOfMonth(), 
            Carbon::now()->endOfMonth()
        ])
        ->whereNotNull('tanggal_surat_kembali')->count();
        
        $totalPesanan_belum_surat_kembali_bulan_ini = Pesanan::whereBetween('created_at', [
            Carbon::now()->startOfMonth(), 
            Carbon::now()->endOfMonth()
        ])
        ->where('tanggal_surat_kembali', null)->count();
        $totalPesanan_sudah_surat_kembali_minggu_ini = Pesanan::whereBetween('created_at', [
            Carbon::now()->startOfWeek(), 
            Carbon::now()->endOfWeek()
        ])
        ->whereNotNull('tanggal_surat_kembali')->count();

        $totalPesanan_belum_surat_kembali_minggu_ini = Pesanan::whereBetween('created_at', [
            Carbon::now()->startOfWeek(), 
            Carbon::now()->endOfWeek()
        ])
        ->where('tanggal_surat_kembali', null)->count();
        return [
            //
            Stat::make('Pesanan Sudah Cetak Surat Kembali Keseluruhan', $totalPesanan_sudah_surat_kembali_keseluruhan)
                ->description('Total pesanan sudah cetak surat kembali keseluruhan')
                ->descriptionIcon('heroicon-m-arrow-trending-up') // Icon tren turun karena uang keluar
                ->color('success'),

            Stat::make('Pesanan Belum Cetak Surat Kembali Keseluruhan', $totalPesanan_belum_surat_kembali_keseluruhan)
                ->description('Total pesanan belum cetak surat kembali keseluruhan')
                ->descriptionIcon('heroicon-m-arrow-trending-down') // Icon tren turun karena uang keluar
                ->color('danger'),
            //
            Stat::make('Pesanan Sudah Cetak Surat Kembali di Bulan Ini', $totalPesanan_sudah_surat_kembali_bulan_ini)
                ->description('Total pesanan sudah cetak surat kembali di bulan ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up') // Icon tren turun karena uang keluar
                ->color('success'),

            Stat::make('Pesanan Belum Cetak Surat Kembali di Bulan Ini', $totalPesanan_belum_surat_kembali_bulan_ini)
                ->description('Total pesanan belum cetak surat kembali di bulan ini')
                ->descriptionIcon('heroicon-m-arrow-trending-down') // Icon tren turun karena uang keluar
                ->color('danger'),
            //
            Stat::make('Pesanan Sudah Cetak Surat Kembali di Minggu Ini', $totalPesanan_sudah_surat_kembali_minggu_ini)
                ->description('Total pesanan sudah cetak surat kembali di minggu ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up') // Icon tren turun karena uang keluar
                ->color('success'),

            Stat::make('Pesanan Belum Cetak Surat Kembali di Minggu Ini', $totalPesanan_belum_surat_kembali_minggu_ini)
                ->description('Total pesanan belum cetak surat kembali di minggu ini')
                ->descriptionIcon('heroicon-m-arrow-trending-down') // Icon tren turun karena uang keluar
                ->color('danger'),
        ];
    }
}
