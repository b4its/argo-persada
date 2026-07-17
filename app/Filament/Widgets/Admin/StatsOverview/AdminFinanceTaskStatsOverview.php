<?php

namespace App\Filament\Widgets\Admin\StatsOverview;

use App\Models\Task;
use App\Filament\Traits\HasDateFilter;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminFinanceTaskStatsOverview extends StatsOverviewWidget
{
    use HasDateFilter;

    protected ?string $heading = 'Ringkasan Tugas Finance';

    protected function getStats(): array
    {
        $range = $this->getFilteredDateRange();

        $tasks = Task::where('role', 'finance')
            ->when($range, fn ($q) => $q->whereBetween('created_at', $range))
            ->with('taskActivities')
            ->get();

        $total = $tasks->count();
        $selesai = $tasks->where('status', 2)->count();

        $rilisDana = $tasks->filter(function ($t) {
            $last = $t->taskActivities->sortByDesc('created_at')->first();
            return $last && (int) $last->pesanan_status === 2;
        })->count();

        $cetakInvoice = $tasks->filter(function ($t) {
            $last = $t->taskActivities->sortByDesc('created_at')->first();
            return $last && (int) $last->pesanan_status === 3;
        })->count();

        $penagihan = $tasks->filter(function ($t) {
            $last = $t->taskActivities->sortByDesc('created_at')->first();
            return $last && (int) $last->pesanan_status === 4;
        })->count();

        $lunas = $tasks->filter(function ($t) {
            $last = $t->taskActivities->sortByDesc('created_at')->first();
            return $last && (int) $last->pesanan_status === 5;
        })->count();

        return [
            Stat::make('Total Tugas', $total)
                ->description('Total tugas Finance')
                ->descriptionIcon('heroicon-o-clipboard-document-list')
                ->color('success'),

            Stat::make('Rilis Dana', $rilisDana)
                ->description('Menunggu rilis dana')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('warning'),

            Stat::make('Cetak Invoice', $cetakInvoice)
                ->description('Perlu cetak invoice')
                ->descriptionIcon('heroicon-o-printer')
                ->color('info'),

            Stat::make('Penagihan', $penagihan)
                ->description('Dalam proses penagihan')
                ->descriptionIcon('heroicon-o-document-text')
                ->color('danger'),

            Stat::make('Lunas', $lunas)
                ->description('Pembayaran lunas')
                ->descriptionIcon('heroicon-o-check-badge')
                ->color('success'),

            Stat::make('Selesai', $selesai)
                ->description('Tugas selesai')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),
        ];
    }
}
