<?php

namespace App\Filament\Widgets\Admin\Charts;

use App\Models\Task;
use App\Models\TaskActivity;
use App\Filament\Traits\HasDateFilter;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class DivisiTaskPieChart extends ChartWidget
{
    use HasDateFilter;

    protected ?string $heading = 'Distribusi Status Tugas';

    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 1;
    protected ?string $maxHeight = '300px';

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<JS
        {
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let total = context.dataset.data.reduce((a, b) => a + b, 0);
                            let value = context.parsed;
                            let pct = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return context.label + ': ' + value + ' (' + pct + '%)';
                        }
                    }
                }
            },
            onClick: function(event, elements) {
                if (elements.length > 0) {
                    let index = elements[0].index;
                    let label = this.data.labels[index];
                    let value = this.data.datasets[0].data[index];
                    window.dispatchEvent(new CustomEvent('chart-clicked', { detail: {
                        chart: 'divisi-task-status', label: label, value: value,
                        datasetLabel: '', index: index
                    } }));
                }
            }
        }
        JS);
    }

    protected function getData(): array
    {
        $range = $this->getFilteredDateRange();

        $roleStatuses = [
            'marketing' => [
                0 => ['label' => 'Marketing Dibuat', 'color' => '#3b82f6'],
                1 => ['label' => 'Marketing Pending', 'color' => '#f59e0b'],
            ],
            'finance' => [
                2 => ['label' => 'Finance Rilis Dana', 'color' => '#f97316'],
                3 => ['label' => 'Finance Cetak Invoice', 'color' => '#8b5cf6'],
                4 => ['label' => 'Finance Penagihan', 'color' => '#ef4444'],
                5 => ['label' => 'Finance Lunas', 'color' => '#10b981'],
            ],
            'logistik' => [
                6 => ['label' => 'Logistik Cetak SJ', 'color' => '#f59e0b'],
                7 => ['label' => 'Logistik Selesai Kirim', 'color' => '#10b981'],
            ],
        ];

        $labels = [];
        $data = [];
        $backgroundColors = [];

        foreach ($roleStatuses as $role => $statuses) {
            $tasks = Task::where('role', $role)
                ->when($range, fn ($q) => $q->whereBetween('created_at', $range))
                ->with('taskActivities')
                ->get();

            foreach ($statuses as $status => $meta) {
                $count = $tasks->filter(function ($t) use ($status) {
                    $lastActivity = $t->taskActivities->sortByDesc('created_at')->first();
                    return $lastActivity && (int) $lastActivity->pesanan_status === $status;
                })->count();

                if ($count > 0) {
                    $labels[] = $meta['label'];
                    $data[] = $count;
                    $backgroundColors[] = $meta['color'];
                }
            }
        }

        return [
            'datasets' => [[
                'data' => $data,
                'backgroundColor' => $backgroundColors,
            ]],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
