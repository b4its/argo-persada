<?php

namespace App\Filament\Widgets\Admin\StatsOverview;

use App\Models\Task;
use App\Filament\Traits\HasDateFilter;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminLogistikTaskStatsOverview extends StatsOverviewWidget
{
    use HasDateFilter;

    protected ?string $heading = 'Ringkasan Tugas Logistik';

    protected function getStats(): array
    {
        $range = $this->getFilteredDateRange();

        $tasks = Task::where('role', 'logistik')
            ->when($range, fn ($q) => $q->whereBetween('created_at', $range))
            ->with('taskActivities')
            ->get();

        $total = $tasks->count();
        $selesai = $tasks->where('status', 2)->count();

        $cetakSJ = $tasks->filter(function ($t) {
            $last = $t->taskActivities->sortByDesc('created_at')->first();
            return $last && (int) $last->pesanan_status === 6;
        })->count();

        $selesaiKirim = $tasks->filter(function ($t) {
            $last = $t->taskActivities->sortByDesc('created_at')->first();
            return $last && (int) $last->pesanan_status === 7;
        })->count();

        return [
            Stat::make('Total Tugas', $total)
                ->description('Total tugas Logistik')
                ->descriptionIcon('heroicon-o-clipboard-document-list')
                ->color('warning'),

            Stat::make('Cetak Surat Jalan', $cetakSJ)
                ->description('Siap cetak surat jalan')
                ->descriptionIcon('heroicon-o-document-duplicate')
                ->color('warning'),

            Stat::make('Selesai Dikirim', $selesaiKirim)
                ->description('Barang sudah dikirim')
                ->descriptionIcon('heroicon-o-truck')
                ->color('info'),

            Stat::make('Selesai', $selesai)
                ->description('Tugas selesai')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),
        ];
    }
}
