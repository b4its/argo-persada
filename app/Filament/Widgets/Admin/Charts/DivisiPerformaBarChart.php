<?php

namespace App\Filament\Widgets\Admin\Charts;

use App\Models\Task;
use App\Filament\Traits\HasDateFilter;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class DivisiPerformaBarChart extends ChartWidget
{
    use HasDateFilter;

    protected ?string $heading = 'Performa Per Divisi';

    public function getDescription(): ?string
    {
        return 'Perbandingan task selesai vs total task per divisi';
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
                        Livewire.dispatch('chart-clicked', { chart: 'divisi-performa', label: label, value: value, datasetLabel: '', index: index });
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

        $totalTasks = [];
        $completedTasks = [];
        $pendingTasks = [];

        foreach ($roles as $role) {
            $queryTotal = Task::where('role', $role);
            $queryCompleted = Task::where('role', $role)->where('status', 2);
            $queryPending = Task::where('role', $role)->where('status', 0);

            if ($range) {
                $queryTotal->whereBetween('created_at', $range);
                $queryCompleted->whereBetween('created_at', $range);
                $queryPending->whereBetween('created_at', $range);
            }

            $totalTasks[] = $queryTotal->count();
            $completedTasks[] = $queryCompleted->count();
            $pendingTasks[] = $queryPending->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Task Selesai',
                    'data' => $completedTasks,
                    'backgroundColor' => '#10b981',
                ],
                [
                    'label' => 'Task Pending',
                    'data' => $pendingTasks,
                    'backgroundColor' => '#f59e0b',
                ],
                [
                    'label' => 'Total Task',
                    'data' => $totalTasks,
                    'backgroundColor' => '#4f46e5',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
