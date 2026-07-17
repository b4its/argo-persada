<?php

namespace App\Filament\Widgets\Admin\Charts;

use App\Models\Pesanan;
use App\Filament\Traits\HasDateFilter;
use Carbon\Carbon;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class PesananLineChart extends ChartWidget
{
    use HasDateFilter;

    protected ?string $heading = 'Trend Pesanan Bulanan';

    public function getDescription(): ?string
    {
        return 'Grafik perkembangan pesanan dalam 6 bulan terakhir';
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<JS
            {
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y + ' pesanan';
                            }
                        }
                    }
                },
                onClick: function(event, elements) {
                    if (elements.length > 0) {
                        let index = elements[0].index;
                        let label = this.data.labels[index];
                        let value = this.data.datasets[0].data[index];
                        window.dispatchEvent(new CustomEvent('chart-clicked', { detail: { chart: 'pesanan-line', label: label, value: value, datasetLabel: '', index: index } }));
                    }
                }
            }
        JS);
    }

    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $range = $this->getFilteredDateRange();
        $months = collect();
        $pesananData = collect();
        $completedData = collect();

        // Ambil data 6 bulan terakhir
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months->push($date->translatedFormat('M Y'));

            $queryTotal = Pesanan::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);
            $queryCompleted = Pesanan::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->where('status_pesanan', 8);

            if ($range) {
                $queryTotal->whereBetween('created_at', $range);
                $queryCompleted->whereBetween('created_at', $range);
            }

            $pesananData->push($queryTotal->count());
            $completedData->push($queryCompleted->count());
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Pesanan',
                    'data' => $pesananData->toArray(),
                    'backgroundColor' => 'rgba(79, 70, 229, 0.8)',
                    'borderColor' => '#4f46e5',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Pesanan Selesai',
                    'data' => $completedData->toArray(),
                    'backgroundColor' => 'rgba(16, 185, 129, 0.8)',
                    'borderColor' => '#10b981',
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
