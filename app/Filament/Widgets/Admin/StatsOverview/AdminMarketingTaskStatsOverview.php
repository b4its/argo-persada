<?php

namespace App\Filament\Widgets\Admin\StatsOverview;

use App\Models\Task;
use App\Filament\Traits\HasDateFilter;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminMarketingTaskStatsOverview extends StatsOverviewWidget
{
    use HasDateFilter;

    protected ?string $heading = 'Ringkasan Tugas Marketing';

    protected function getStats(): array
    {
        $range = $this->getFilteredDateRange();

        $tasks = Task::where('role', 'marketing')
            ->when($range, fn ($q) => $q->whereBetween('created_at', $range))
            ->with('taskActivities')
            ->get();

        $total = $tasks->count();
        $selesai = $tasks->where('status', 2)->count();

        $dibuat = $tasks->filter(function ($t) {
            $last = $t->taskActivities->sortByDesc('created_at')->first();
            return $last && (int) $last->pesanan_status === 0;
        })->count();

        $pending = $tasks->filter(function ($t) {
            $last = $t->taskActivities->sortByDesc('created_at')->first();
            return $last && (int) $last->pesanan_status === 1;
        })->count();

        return [
            Stat::make('Total Tugas', $total)
                ->description('Total tugas Marketing')
                ->descriptionIcon('heroicon-o-clipboard-document-list')
                ->color('info'),

            Stat::make('Dibuat', $dibuat)
                ->description('Pesanan baru dibuat')
                ->descriptionIcon('heroicon-o-plus-circle')
                ->color('info'),

            Stat::make('Pending', $pending)
                ->description('Menunggu proses')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Selesai', $selesai)
                ->description('Tugas selesai')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),
        ];
    }
}
