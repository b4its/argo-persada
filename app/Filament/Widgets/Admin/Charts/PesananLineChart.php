<?php

namespace App\Filament\Widgets\Admin\Charts;

use App\Models\Pesanan;
use Carbon\Carbon;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class PesananLineChart extends ChartWidget
{
    protected ?string $heading = 'Trend Pesanan Bulanan';

    public function getDescription(): ?string
    {
        return 'Grafik perkembangan pesanan dalam 6 bulan terakhir';
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
                        Livewire.dispatch('chart-clicked', { chart: 'pesanan-line', label: label, value: value, datasetLabel: '', index: index });
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
        $months = collect();
        $pesananData = collect();
        $completedData = collect();

        // Ambil data 6 bulan terakhir
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months->push($date->translatedFormat('M Y'));

            // Total pesanan per bulan
            $totalPesanan = Pesanan::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $pesananData->push($totalPesanan);

            // Pesanan selesai per bulan (status_pesanan = 8)
            $completedPesanan = Pesanan::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->where('status_pesanan', 8)
                ->count();
            $completedData->push($completedPesanan);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Pesanan',
                    'data' => $pesananData->toArray(),
                    'borderColor' => '#4f46e5',
                    'backgroundColor' => 'rgba(79, 70, 229, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Pesanan Selesai',
                    'data' => $completedData->toArray(),
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $months->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
