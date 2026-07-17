<?php

namespace App\Filament\Traits;

use Carbon\Carbon;

trait HasDateFilter
{
    public function getListeners(): array
    {
        return array_merge(
            (array) ($this->listeners ?? []),
            ['dashboard-filter-changed' => '$refresh']
        );
    }

    public function getFilteredDateRange(): ?array
    {
        $startDate = session('dashboard_filter_start_date');
        $endDate = session('dashboard_filter_end_date');

        if (!$startDate || !$endDate) {
            return null;
        }

        return [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay(),
        ];
    }
}
