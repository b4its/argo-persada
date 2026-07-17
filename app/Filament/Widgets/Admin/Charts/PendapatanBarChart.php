<?php

namespace App\Filament\Widgets\Admin\Charts;

use App\Models\Pesanan;
use App\Filament\Traits\HasDateFilter;
use Carbon\Carbon;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class PendapatanBarChart extends ChartWidget
{
    use HasDateFilter;

    protected ?string $heading = 'Pendapatan Bulanan';

    public function getDescription(): ?string
    {
        return 'Total nilai pesanan per bulan (dalam juta rupiah)';
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<JS
            {
                onClick: function(event, elements) {
                    if (elements.length > 0) {
                        let index = elements[0].index;
                        let label = this.data.labels[index];
                        let value = this.data.datasets[0].data[index];
                        Livewire.dispatch('chart-clicked', { chart: 'pendapatan-bar', label: label, value: value, datasetLabel: '', index: index });
                    }
                }
            }
        JS);
    }

    protected static ?int $sort = 6;
    protected int | string | array $columnSpan = 1;
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $range = $this->getFilteredDateRange();
        $months = collect();
        $pendapatanData = collect();

        // Ambil data 6 bulan terakhir
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months->push($date->translatedFormat('M Y'));

            $query = Pesanan::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);
            if ($range) {
                $query->whereBetween('created_at', $range);
            }

            $totalPendapatan = $query->sum('total_harga');
            $pendapatanData->push(round($totalPendapatan / 1000000, 2));
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan (Juta Rp)',
                    'data' => $pendapatanData->toArray(),
                    'backgroundColor' => [
                        '#4f46e5', '#6366f1', '#818cf8',
                        '#a5b4fc', '#c7d2fe', '#e0e7ff',
                    ],
                    'borderColor' => '#4f46e5',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $months->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
