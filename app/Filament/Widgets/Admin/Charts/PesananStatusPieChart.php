<?php

namespace App\Filament\Widgets\Admin\Charts;

use App\Models\Pesanan;
use App\Filament\Traits\HasDateFilter;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class PesananStatusPieChart extends ChartWidget
{
    use HasDateFilter;

    protected ?string $heading = 'Status Pesanan';

    public function getDescription(): ?string
    {
        return 'Distribusi status pesanan saat ini';
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
                        window.dispatchEvent(new CustomEvent('chart-clicked', { detail: { chart: 'status-pie', label: label, value: value, datasetLabel: '', index: index } }));
                    }
                }
            }
        JS);
    }

    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 1;
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $range = $this->getFilteredDateRange();
        $statusLabels = [
            0 => 'Dibuat',
            1 => 'Pending',
            2 => 'Perlu Rilis Dana',
            3 => 'Perlu Cetak Invoice',
            4 => 'Perlu Penagihan',
            5 => 'Ditandai Lunas',
            6 => 'Cetak Surat Jalan',
            7 => 'Selesai Dikirim',
            8 => 'Selesai',
        ];

        $colors = [
            '#6b7280', '#f59e0b', '#3b82f6', '#8b5cf6', '#ec4899',
            '#14b8a6', '#f97316', '#06b6d4', '#10b981',
        ];

        $data = [];
        $labels = [];
        $backgroundColors = [];

        foreach ($statusLabels as $status => $label) {
            $query = Pesanan::where('status_pesanan', $status);
            if ($range) {
                $query->whereBetween('created_at', $range);
            }
            $count = $query->count();
            if ($count > 0) {
                $data[] = $count;
                $labels[] = $label;
                $backgroundColors[] = $colors[$status];
            }
        }

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
