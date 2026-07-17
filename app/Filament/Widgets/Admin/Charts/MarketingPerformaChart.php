<?php

namespace App\Filament\Widgets\Admin\Charts;

use App\Models\Pesanan;
use App\Models\User;
use App\Filament\Traits\HasDateFilter;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class MarketingPerformaChart extends ChartWidget
{
    use HasDateFilter;

    protected ?string $heading = 'Performa Marketing';

    public function getHeading(): string|Htmlable|null
    {
        return new HtmlString(
            '<div class="flex items-center justify-between w-full gap-4">' .
            '<span>Performa Marketing</span>' .
            '<button type="button" id="marketing-overall-btn" ' .
            'class="inline-flex items-center gap-1.5 text-xs font-medium text-white bg-primary-600 hover:bg-primary-500 rounded-lg px-3 py-1.5 transition shadow-sm">' .
            '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>' .
            'Lihat Keseluruhan</button>' .
            '</div>'
        );
    }

    public function getDescription(): string|Htmlable|null
    {
        return 'Pendapatan marketing dari pesanan lunas/selesai dengan kas harian debet';
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<JS
            {
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.parsed || 0;
                                let formatted = 'Rp ' + value.toLocaleString('id-ID');
                                return label + ': ' + formatted;
                            },
                            afterLabel: function(context) {
                                let index = context.dataIndex;
                                let orders = context.dataset.orderCount ? context.dataset.orderCount[index] : 0;
                                let avgDays = context.dataset.avgProcessingDays ? context.dataset.avgProcessingDays[index] : 0;
                                let slaStatus = context.dataset.slaStatus ? context.dataset.slaStatus[index] : '';
                                return [
                                    'Total pesanan: ' + orders,
                                    'Rata-rata proses: ' + avgDays + ' hari',
                                    'Tepat waktu \u226448 jam: ' + slaStatus
                                ];
                            }
                        }
                    },
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 12,
                            padding: 8,
                            font: { size: 11 }
                        }
                    },
                    datalabels: {
                        display: true,
                        color: '#fff',
                        font: {
                            weight: 'bold',
                            size: 11
                        },
                        formatter: function(value, ctx) {
                            let label = ctx.chart.data.labels[ctx.dataIndex];
                            let parts = label.split(' ');
                            return parts.length > 1 ? parts[0] : label;
                        }
                    }
                },
                onClick: function(event, elements) {
                    if (elements.length > 0) {
                        let index = elements[0].index;
                        let label = this.data.labels[index];
                        let value = this.data.datasets[0].data[index];
                        window.dispatchEvent(new CustomEvent('chart-clicked', { detail: { chart: 'marketing-performa', label: label, datasetLabel: '', value: value, index: index } }));
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
        $range = $this->getFilteredDateRange();
        $marketingUsers = User::where('role', 'marketing')->get();

        $colors = [
            '#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6',
            '#06b6d4', '#ec4899', '#14b8a6', '#f97316', '#6366f1'
        ];

        $labels = [];
        $data = [];
        $bgColors = [];
        $orderCounts = [];
        $avgProcessingDays = [];
        $slaStatuses = [];

        $colorIndex = 0;
        foreach ($marketingUsers as $user) {
            $pesananIds = Pesanan::where('user_id', $user->id)
                ->where(function ($q) {
                    $q->where('status_pesanan', 5)
                      ->orWhere('status_pesanan', 8);
                })
                ->whereHas('kasHarian', function ($q) {
                    $q->where('debet', '>', 0);
                });

            if ($range) {
                $pesananIds->whereBetween('created_at', $range);
            }

            $totalRevenue = (float) $pesananIds->sum('total_harga');

            if ($totalRevenue > 0) {
                $orders = Pesanan::where('user_id', $user->id)
                    ->where(function ($q) {
                        $q->where('status_pesanan', 5)
                          ->orWhere('status_pesanan', 8);
                    })
                    ->whereHas('kasHarian', function ($q) {
                        $q->where('debet', '>', 0);
                    });

                if ($range) {
                    $orders->whereBetween('created_at', $range);
                }

                $orderList = $orders->get(['created_at', 'tanggal_terbit_surat_jalan']);
                $count = $orderList->count();
                $totalDays = 0;
                $withinSla = 0;

                foreach ($orderList as $o) {
                    if ($o->created_at && $o->tanggal_terbit_surat_jalan) {
                        $diffDays = $o->created_at->diffInDays($o->tanggal_terbit_surat_jalan);
                        $totalDays += $diffDays;
                        if ($diffDays <= 2) {
                            $withinSla++;
                        }
                    }
                }

                $labels[] = $user->name;
                $data[] = round($totalRevenue, 2);
                $bgColors[] = $colors[$colorIndex % count($colors)];
                $orderCounts[] = $count;
                $avgProcessingDays[] = $count > 0 ? round($totalDays / $count, 1) : 0;
                $slaStatuses[] = $count > 0 ? round(($withinSla / $count) * 100) . '%' : '0%';
                $colorIndex++;
            }
        }

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $bgColors,
                    'orderCount' => $orderCounts,
                    'avgProcessingDays' => $avgProcessingDays,
                    'slaStatus' => $slaStatuses,
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
