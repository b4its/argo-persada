<?php

namespace App\Filament\Widgets\Admin\Charts;

use App\Models\Task;
use App\Filament\Traits\HasDateFilter;
use Carbon\Carbon;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class DivisiPerformaBarChart extends ChartWidget
{
    use HasDateFilter;

    protected ?string $heading = 'Performa Batas Waktu Per Divisi';

    public function getDescription(): ?string
    {
        return 'Tugas diselesaikan, terlambat, dan dalam proses berdasarkan batas waktu 2x24 jam';
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<JS
            {
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y;
                            }
                        }
                    }
                },
                onClick: function(event, elements) {
                    if (elements.length > 0) {
                        let index = elements[0].index;
                        let label = this.data.labels[index];
                        let value = this.data.datasets[0].data[index];
                        window.dispatchEvent(new CustomEvent('chart-clicked', { detail: { chart: 'divisi-performa', label: label, value: value, datasetLabel: '', index: index } }));
                    }
                }
            }
        JS);
    }

    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 1;
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $range = $this->getFilteredDateRange();
        $roles = ['marketing', 'finance', 'logistik'];
        $labels = ['Marketing', 'Finance', 'Logistik'];

        $diselesaikan = [];
        $terlambat = [];
        $dalamProses = [];

        $now = Carbon::now();

        foreach ($roles as $role) {
            $tasks = Task::where('role', $role)
                ->when($range, fn ($q) => $q->whereBetween('created_at', $range))
                ->get();

            $onTime = 0;
            $late = 0;
            $inProgress = 0;

            foreach ($tasks as $task) {
                $hoursSinceCreation = $task->created_at->diffInHours($now);

                if ($task->status === 2) {
                    $hoursToComplete = $task->created_at->diffInHours($task->updated_at);
                    if ($hoursToComplete <= 48) {
                        $onTime++;
                    } else {
                        $late++;
                    }
                } elseif ($task->status === 1) {
                    if ($hoursSinceCreation <= 48) {
                        $inProgress++;
                    } else {
                        $late++;
                    }
                } else {
                    $inProgress++;
                }
            }

            $diselesaikan[] = $onTime;
            $terlambat[] = $late;
            $dalamProses[] = $inProgress;
        }

        $datasets = [
            [
                'label' => 'Diselesaikan',
                'data' => $diselesaikan,
                'backgroundColor' => '#10b981',
            ],
            [
                'label' => 'Terlambat',
                'data' => $terlambat,
                'backgroundColor' => '#f59e0b',
            ],
        ];

        if (array_sum($dalamProses) > 0) {
            $datasets[] = [
                'label' => 'Dalam Proses',
                'data' => $dalamProses,
                'backgroundColor' => '#3b82f6',
            ];
        }

        return [
            'datasets' => $datasets,
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
