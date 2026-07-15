<?php

namespace App\Filament\Widgets\Admin\Charts;

use App\Models\Pesanan;
use Carbon\Carbon;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class TipePesananPieChart extends ChartWidget
{
    protected ?string $heading = 'Tipe Pesanan';

    public function getDescription(): ?string
    {
        return 'Perbandingan pesanan Supply vs Projek';
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
                        Livewire.dispatch('chart-clicked', { chart: 'tipe-pie', label: label, value: value, datasetLabel: '', index: index });
                    }
                }
            }
        JS);
    }

    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 1;
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $supplyCount = Pesanan::where('tipe_pesanan', 0)->count();
        $projekCount = Pesanan::where('tipe_pesanan', 1)->count();

        return [
            'datasets' => [
                [
                    'data' => [$supplyCount, $projekCount],
                    'backgroundColor' => ['#3b82f6', '#8b5cf6'],
                ],
            ],
            'labels' => ['Supply', 'Projek'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
