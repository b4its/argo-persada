<?php

namespace App\Filament\Widgets\Admin\Charts;

use App\Models\Task;
use App\Models\Pesanan;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class DivisiPerformaBarChart extends ChartWidget
{
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
        $roles = ['marketing', 'finance', 'logistik'];
        $labels = ['Marketing', 'Finance', 'Logistik'];
        
        $totalTasks = [];
        $completedTasks = [];
        $pendingTasks = [];

        foreach ($roles as $role) {
            $total = Task::where('role', $role)->count();
            $completed = Task::where('role', $role)->where('status', 2)->count();
            $pending = Task::where('role', $role)->where('status', 0)->count();

            $totalTasks[] = $total;
            $completedTasks[] = $completed;
            $pendingTasks[] = $pending;
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
