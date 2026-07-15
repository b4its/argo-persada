<?php

namespace App\Filament\Widgets\Admin\Charts;

use App\Models\Pesanan;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class PesananStatusPieChart extends ChartWidget
{
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
                        Livewire.dispatch('chart-clicked', { chart: 'status-pie', label: label, value: value, datasetLabel: '', index: index });
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
            '#6b7280', // Dibuat - gray
            '#f59e0b', // Pending - amber
            '#3b82f6', // Perlu Rilis Dana - blue
            '#8b5cf6', // Perlu Cetak Invoice - violet
            '#ec4899', // Perlu Penagihan - pink
            '#14b8a6', // Ditandai Lunas - teal
            '#f97316', // Cetak Surat Jalan - orange
            '#06b6d4', // Selesai Dikirim - cyan
            '#10b981', // Selesai - emerald
        ];

        $data = [];
        $labels = [];
        $backgroundColors = [];

        foreach ($statusLabels as $status => $label) {
            $count = Pesanan::where('status_pesanan', $status)->count();
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
