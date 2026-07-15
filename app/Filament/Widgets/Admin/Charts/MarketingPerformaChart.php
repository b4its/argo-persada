<?php

namespace App\Filament\Widgets\Admin\Charts;

use App\Models\Pesanan;
use App\Models\User;
use Carbon\Carbon;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class MarketingPerformaChart extends ChartWidget
{
    protected ?string $heading = 'Performa Marketing';

    public function getHeading(): string|Htmlable|null
    {
        return new HtmlString(
            '<div class="flex items-center justify-between w-full gap-4">' .
            '<span>Performa Marketing</span>' .
            '<button type="button" onclick="Livewire.dispatch(\'chart-overall-clicked\')" ' .
            'class="inline-flex items-center gap-1.5 text-xs font-medium text-white bg-primary-600 hover:bg-primary-500 rounded-lg px-3 py-1.5 transition shadow-sm">' .
            '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>' .
            'Lihat Keseluruhan</button>' .
            '</div>'
        );
    }

    public function getDescription(): string|Htmlable|null
    {
        return 'Jumlah pesanan yang dibuat oleh tim marketing per bulan';
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<JS
            {
                onClick: function(event, elements) {
                    if (elements.length > 0) {
                        let index = elements[0].index;
                        let datasetIndex = elements[0].datasetIndex;
                        let label = this.data.labels[index];
                        let datasetLabel = this.data.datasets[datasetIndex]?.label || '';
                        let value = this.data.datasets[datasetIndex]?.data[index] || 0;
                        Livewire.dispatch('chart-clicked', { chart: 'marketing-performa', label: label, datasetLabel: datasetLabel, value: value, index: index });
                    }
                }
            }
        JS);
    }

    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $marketingUsers = User::where('role', 'marketing')->get();
        
        $months = collect();
        $datasets = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months->push($date->translatedFormat('M Y'));
        }

        $colors = [
            '#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', 
            '#06b6d4', '#ec4899', '#14b8a6', '#f97316', '#6366f1'
        ];

        $colorIndex = 0;
        foreach ($marketingUsers as $user) {
            $userData = collect();

            for ($i = 5; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $count = Pesanan::where('user_id', $user->id)
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();
                $userData->push($count);
            }

            $datasets[] = [
                'label' => $user->name,
                'data' => $userData->toArray(),
                'borderColor' => $colors[$colorIndex % count($colors)],
                'backgroundColor' => $colors[$colorIndex % count($colors)] . '20',
                'fill' => false,
                'tension' => 0.3,
            ];
            $colorIndex++;
        }

        if (empty($datasets)) {
            $totalData = collect();
            for ($i = 5; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $count = Pesanan::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();
                $totalData->push($count);
            }

            $datasets[] = [
                'label' => 'Total Pesanan',
                'data' => $totalData->toArray(),
                'borderColor' => '#4f46e5',
                'backgroundColor' => 'rgba(79, 70, 229, 0.1)',
                'fill' => true,
                'tension' => 0.3,
            ];
        }

        return [
            'datasets' => $datasets,
            'labels' => $months->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
