<?php

namespace App\Filament\Pages\Admin;

use App\Models\Pesanan;
use App\Models\Task;
use App\Models\TaskActivity;
use App\Models\User;
use App\Models\LogActivities;
use Carbon\Carbon;
use Filament\Pages\Page;
use App\Filament\Traits\HasDateFilter;
use App\Filament\Widgets\Admin\StatsOverview\AdminPesananStatsOverview;
use App\Filament\Widgets\Admin\StatsOverview\AdminKasHarianStatsOverview;
use App\Filament\Widgets\Admin\StatsOverview\AdminAkunStatsOverview;
use App\Filament\Widgets\Admin\AdminTaskTables;
use App\Filament\Widgets\Admin\Charts\PesananLineChart;
use App\Filament\Widgets\Admin\Charts\MarketingPerformaChart;
use App\Filament\Widgets\Admin\Charts\DivisiPerformaBarChart;
use App\Filament\Widgets\Admin\Charts\PesananStatusPieChart;
use App\Filament\Widgets\Admin\Charts\TipePesananPieChart;
use App\Filament\Widgets\Admin\Charts\PendapatanBarChart;

class AdminDashboard extends Page
{
    use HasDateFilter;

    protected static ?string $title = 'Dashboard Admin';

    protected string $view = 'filament.pages.admin.admin-dashboard';

    public array $roleProgressData = [];
    public array $recentActivities = [];
    public array $roleTables = [];
    public ?array $detailData = null;
    public bool $showDetailModal = false;
    public ?array $divisiDetailData = null;
    public bool $showDivisiDetailModal = false;
    public ?array $chartDetailData = null;
    public bool $showChartDetailModal = false;
    public array $unparticipatedUsers = [];
    public ?array $marketingOverallData = null;
    public bool $showMarketingOverallModal = false;

    public int $recentActivitiesPage = 1;
    public int $recentActivitiesLastPage = 1;

    public array $roleProgressPage = ['marketing' => 1, 'finance' => 1, 'logistik' => 1];

    public array $roleTablePage = ['marketing' => 1, 'finance' => 1, 'logistik' => 1];
    public array $roleTableLastPage = ['marketing' => 1, 'finance' => 1, 'logistik' => 1];

    public int $marketingOverallPage = 1;
    public int $marketingOverallLastPage = 1;

    public string $filterPreset = '';
    public ?string $filterStartDate = null;
    public ?string $filterEndDate = null;

    public function mount(): void
    {
        $this->filterPreset = session('dashboard_filter_preset', '');
        $this->filterStartDate = session('dashboard_filter_start_date', '');
        $this->filterEndDate = session('dashboard_filter_end_date', '');

        $this->loadRoleProgress();
        $this->loadRecentActivities();
        $this->loadRoleTables();
        $this->loadUnparticipatedUsers();
    }

    public function getRenderHookScopes(): array
    {
        return ['admin-dashboard'];
    }

    public function updatedFilterPreset(): void
    {
        $now = Carbon::now();

        match ($this->filterPreset) {
            '7days' => [
                $this->filterStartDate = $now->copy()->subDays(7)->format('Y-m-d'),
                $this->filterEndDate = $now->format('Y-m-d'),
            ],
            '2weeks' => [
                $this->filterStartDate = $now->copy()->subWeeks(2)->format('Y-m-d'),
                $this->filterEndDate = $now->format('Y-m-d'),
            ],
            '3weeks' => [
                $this->filterStartDate = $now->copy()->subWeeks(3)->format('Y-m-d'),
                $this->filterEndDate = $now->format('Y-m-d'),
            ],
            '1month' => [
                $this->filterStartDate = $now->copy()->subMonth()->format('Y-m-d'),
                $this->filterEndDate = $now->format('Y-m-d'),
            ],
            default => [
                $this->filterStartDate = null,
                $this->filterEndDate = null,
            ],
        };

        session([
            'dashboard_filter_preset' => $this->filterPreset,
            'dashboard_filter_start_date' => $this->filterStartDate,
            'dashboard_filter_end_date' => $this->filterEndDate,
        ]);

        $this->js('window.location.reload()');
    }

    public function applyCustomFilter(): void
    {
        $this->filterPreset = 'custom';

        session([
            'dashboard_filter_preset' => 'custom',
            'dashboard_filter_start_date' => $this->filterStartDate,
            'dashboard_filter_end_date' => $this->filterEndDate,
        ]);

        $this->js('window.location.reload()');
    }

    public function setRecentActivitiesPage(int $page): void
    {
        $this->recentActivitiesPage = max(1, min($page, $this->recentActivitiesLastPage));
        $this->loadRecentActivities();
    }

    public function setRoleProgressPage(string $role, int $page): void
    {
        $this->roleProgressPage[$role] = max(1, $page);
        $this->loadRoleProgress();
    }

    public function setRoleTablePage(string $role, int $page): void
    {
        $this->roleTablePage[$role] = max(1, min($page, $this->roleTableLastPage[$role]));
        $this->loadRoleTables();
    }

    public function setMarketingOverallPage(int $page): void
    {
        $this->marketingOverallPage = max(1, $page);
        $this->showMarketingOverall();
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AdminPesananStatsOverview::class,
            AdminKasHarianStatsOverview::class,
            AdminAkunStatsOverview::class,
            PesananLineChart::class,
            MarketingPerformaChart::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            DivisiPerformaBarChart::class,
            PesananStatusPieChart::class,
            TipePesananPieChart::class,
            PendapatanBarChart::class,
            AdminTaskTables::class,
        ];
    }

    public function showDetail(int $pesananId): void
    {
        $pesanan = Pesanan::with([
            'user',
            'companyInternal',
            'tasks.taskActivities.createdUser',
            'tasks.taskActivities.updatedUser',
            'keranjang.queueKeranjang',
            'kasHarian',
            'bukuBesar',
        ])->find($pesananId);

        if (!$pesanan) {
            $this->showDetailModal = false;
            $this->detailData = null;
            return;
        }

        $roleData = [];
        foreach (['marketing', 'finance', 'logistik'] as $role) {
            $task = $pesanan->tasks->where('role', $role)->first();
            $users = [];
            $activities = [];

            if ($task) {
                $statusLabel = match ((int) $task->status) { 0 => 'Pending', 1 => 'Proses', 2 => 'Selesai', default => '-' };
                $statusColor = match ((int) $task->status) { 0 => 'gray', 1 => 'warning', 2 => 'success', default => 'gray' };

                foreach ($task->taskActivities->sortBy('created_at') as $a) {
                    $actor = $a->createdUser?->name ?? $a->updatedUser?->name ?? 'System';
                    if ($a->createdUser) $users[] = $a->createdUser->name;
                    if ($a->updatedUser) $users[] = $a->updatedUser?->name;
                    $activities[] = [
                        'user' => $actor,
                        'time' => $a->created_at->format('d M Y H:i'),
                        'note' => $a->note ?? '',
                    ];
                }

                $roleData[$role] = [
                    'status' => $statusLabel,
                    'status_color' => $statusColor,
                    'users' => array_values(array_unique(array_filter($users))),
                    'activities' => $activities,
                ];
            } else {
                $roleData[$role] = [
                    'status' => '-',
                    'status_color' => 'gray',
                    'users' => [],
                    'activities' => [],
                ];
            }
        }

        $items = [];
        foreach ($pesanan->keranjang?->queueKeranjang ?? [] as $qi) {
            $items[] = [
                'name' => $qi->item_name,
                'qty' => $qi->quantity,
                'satuan' => $qi->satuan ?? '',
                'po' => $qi->po,
                'supplier' => $qi->supplier_name ?? '-',
            ];
        }

        $kasData = [];
        foreach ($pesanan->kasHarian ?? [] as $k) {
            $kasData[] = [
                'kode' => $k->kode,
                'tipe' => $k->tipe ?? '-',
                'jumlah' => $k->jumlah,
                'keterangan' => $k->keterangan ?? '-',
                'tanggal' => $k->created_at?->format('d M Y'),
            ];
        }

        $bukuData = [];
        foreach ($pesanan->bukuBesar ?? [] as $b) {
            $bukuData[] = [
                'kode' => $b->kode ?? '-',
                'deskripsi' => $b->deskripsi ?? '-',
                'debit' => $b->debit,
                'kredit' => $b->kredit,
                'tanggal' => $b->created_at?->format('d M Y'),
            ];
        }

        $metodeRilis = match ((int) ($pesanan->metode_pembayaran_rilis_dana ?? 0)) {
            0 => 'Tunai', 1 => 'Kredit (30 hari)', 2 => 'Kredit', 3 => 'Debit', default => '-',
        };
        $metodeLunas = match ((int) ($pesanan->metode_pembayaran_lunas ?? 0)) {
            0 => 'Tunai', 1 => 'Kredit', 2 => 'Kredit', 3 => 'Debit', default => '-',
        };
        $statusRilis = match ((int) ($pesanan->status_perilisan_dana ?? 0)) {
            0 => 'Belum Rilis', 1 => 'Proses Rilis', 2 => 'Sudah Rilis', 3 => 'Tidak Perlu Rilis', default => '-',
        };

        $this->detailData = [
            // Informasi Umum
            'code' => $pesanan->code,
            'no_po' => $pesanan->no_po ?: '-',
            'no_requisition' => $pesanan->no_requisition ?: '-',
            'no_invoice' => $pesanan->no_invoice ?: '-',
            'no_delivery_order' => $pesanan->no_delivery_order ?: '-',
            'group_name' => $pesanan->group_name ?? '-',
            'company' => $pesanan->company_name ?? '-',
            'address' => $pesanan->address ?? '-',

            // Keuangan
            'ppn' => $pesanan->ppn ?? '0',
            'total_harga' => $pesanan->total_harga ?? 0,
            'total_formatted' => 'Rp ' . number_format($pesanan->total_harga ?? 0, 0, ',', '.'),
            'metode_rilis' => $metodeRilis,
            'metode_lunas' => $metodeLunas,
            'status_rilis' => $statusRilis,

            // Status
            'status' => match ((int) $pesanan->pesanan_status) {
                0 => 'Dibuat', 1 => 'Pending', 2 => 'Perlu Rilis Dana',
                3 => 'Perlu Cetak Invoice', 4 => 'Perlu Penagihan',
                5 => 'Ditandai Lunas', 6 => 'Cetak Surat Jalan',
                7 => 'Selesai Dikirim', 8 => 'Selesai', default => 'Unknown',
            },

            // Tanggal-tanggal
            'created_at' => $pesanan->created_at?->format('d M Y H:i'),
            'created_at_raw' => $pesanan->created_at,
            'tanggal_rilis_dana' => $pesanan->tanggal_rilis_dana ? Carbon::parse($pesanan->tanggal_rilis_dana)->format('d M Y') : '-',
            'tanggal_rilis_dana_raw' => $pesanan->tanggal_rilis_dana ? Carbon::parse($pesanan->tanggal_rilis_dana) : null,
            'tanggal_terbit_invoice' => $pesanan->tanggal_terbit_invoice ? Carbon::parse($pesanan->tanggal_terbit_invoice)->format('d M Y') : '-',
            'tanggal_terbit_invoice_raw' => $pesanan->tanggal_terbit_invoice ? Carbon::parse($pesanan->tanggal_terbit_invoice) : null,
            'tanggal_jatuh_tempo' => $pesanan->tanggal_jatuh_tempo ? Carbon::parse($pesanan->tanggal_jatuh_tempo)->format('d M Y') : '-',
            'tanggal_terbit_surat_jalan' => $pesanan->tanggal_terbit_surat_jalan ? Carbon::parse($pesanan->tanggal_terbit_surat_jalan)->format('d M Y') : '-',
            'tanggal_terbit_surat_jalan_raw' => $pesanan->tanggal_terbit_surat_jalan ? Carbon::parse($pesanan->tanggal_terbit_surat_jalan) : null,
            'tanggal_surat_kembali' => $pesanan->tanggal_surat_kembali ? Carbon::parse($pesanan->tanggal_surat_kembali)->format('d M Y') : '-',
            'tanggal_surat_kembali_raw' => $pesanan->tanggal_surat_kembali ? Carbon::parse($pesanan->tanggal_surat_kembali) : null,
            'tanggal_lunas' => $pesanan->tanggal_lunas ? Carbon::parse($pesanan->tanggal_lunas)->format('d M Y') : '-',
            'tanggal_lunas_raw' => $pesanan->tanggal_lunas ? Carbon::parse($pesanan->tanggal_lunas) : null,

            // Jarak antar tahapan
            'po_to_do_diff' => $this->diffHuman($pesanan->created_at, $pesanan->tanggal_terbit_surat_jalan),
            'po_to_rilis_diff' => $this->diffHuman($pesanan->created_at, $pesanan->tanggal_rilis_dana),
            'rilis_to_invoice_diff' => $this->diffHuman($pesanan->tanggal_rilis_dana, $pesanan->tanggal_terbit_invoice),
            'invoice_to_do_diff' => $this->diffHuman($pesanan->tanggal_terbit_invoice, $pesanan->tanggal_terbit_surat_jalan),
            'do_to_kembali_diff' => $this->diffHuman($pesanan->tanggal_terbit_surat_jalan, $pesanan->tanggal_surat_kembali),
            'kembali_to_lunas_diff' => $this->diffHuman($pesanan->tanggal_surat_kembali, $pesanan->tanggal_lunas),

            // Pembuat
            'created_by' => $pesanan->user?->name ?? '-',

            // Company Internal (Bank)
            'bank' => $pesanan->companyInternal ? [
                'nama' => $pesanan->companyInternal->nama ?? '-',
                'no_rekening' => $pesanan->companyInternal->no_rekening ?? '-',
                'nama_bank' => $pesanan->companyInternal->nama_bank ?? '-',
            ] : null,

            // Relasi
            'items' => $items,
            'kas_harian' => $kasData,
            'buku_besar' => $bukuData,
            'roles' => $roleData,
        ];
        $this->showDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->detailData = null;
    }

    public function showDivisiDetail(string $role): void
    {
        $roleLabel = match ($role) {
            'marketing' => 'Marketing',
            'finance' => 'Finance',
            'logistik' => 'Logistik',
            default => ucfirst($role),
        };

        $users = User::where('role', $role)->get();

        // Ambil semua task untuk role ini
        $tasks = Task::where('role', $role)
            ->with([
                'pesanan',
                'taskActivities.createdUser',
                'taskActivities.updatedUser',
            ])
            ->latest('updated_at')
            ->get();

        // Kelompokkan aktivitas per user
        $userActivities = [];
        foreach ($users as $user) {
            $userTaskActivities = TaskActivity::where(function ($q) use ($user) {
                    $q->where('created_user_id', $user->id)
                      ->orWhere('updated_user_id', $user->id);
                })
                ->whereHas('task', fn ($q) => $q->where('role', $role))
                ->with(['task.pesanan', 'createdUser', 'updatedUser'])
                ->latest()
                ->get();

            $activities = [];
            foreach ($userTaskActivities as $a) {
                $activities[] = [
                    'pesanan_code' => $a->task?->pesanan?->code ?? '-',
                    'note' => $a->note ?? '-',
                    'time' => $a->created_at->format('d M Y H:i'),
                    'pesanan_status' => $a->pesanan_status,
                    'as_creator' => $a->created_user_id === $user->id,
                ];
            }

            $userActivities[] = [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'total_activities' => count($activities),
                'activities' => $activities,
            ];
        }

        // Statistik role
        $totalTasks = Task::where('role', $role)->count();
        $completedTasks = Task::where('role', $role)->where('status', 2)->count();
        $pendingTasks = Task::where('role', $role)->where('status', 0)->count();
        $inProgressTasks = Task::where('role', $role)->where('status', 1)->count();

        // Grafik progress per user
        $userStats = [];
        foreach ($users as $user) {
            $userTaskCount = Task::where('role', $role)
                ->whereHas('taskActivities', fn ($q) => $q->where('created_user_id', $user->id))
                ->count();
            $userCompletedCount = Task::where('role', $role)
                ->where('status', 2)
                ->whereHas('taskActivities', fn ($q) => $q->where('created_user_id', $user->id))
                ->count();

            if ($userTaskCount > 0) {
                $userStats[] = [
                    'name' => $user->name,
                    'total' => $userTaskCount,
                    'completed' => $userCompletedCount,
                    'percentage' => round(($userCompletedCount / $userTaskCount) * 100),
                ];
            }
        }

        $this->divisiDetailData = [
            'role' => $role,
            'role_label' => $roleLabel,
            'users' => $users->map(fn ($u) => ['id' => $u->id, 'name' => $u->name, 'email' => $u->email])->toArray(),
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'pending_tasks' => $pendingTasks,
            'in_progress_tasks' => $inProgressTasks,
            'completion_percentage' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0,
            'user_activities' => $userActivities,
            'user_stats' => $userStats,
            'tasks' => $tasks->map(fn ($t) => [
                'id' => $t->id,
                'pesanan_code' => $t->pesanan?->code ?? '-',
                'title' => $t->title,
                'status' => (int) $t->status,
                'status_label' => match ((int) $t->status) { 0 => 'Pending', 1 => 'Proses', 2 => 'Selesai', default => '-' },
                'due_date' => $t->due_date?->format('d M Y'),
                'created_at' => $t->created_at->format('d M Y H:i'),
            ])->toArray(),
        ];

        $this->showDivisiDetailModal = true;
    }

    public function closeDivisiDetail(): void
    {
        $this->showDivisiDetailModal = false;
        $this->divisiDetailData = null;
    }

    public function showChartDetail(string $chart, string $label, int|float $value, string $datasetLabel = '', int $index = 0): void
    {
        $title = match ($chart) {
            'pesanan-line' => "Pesanan Bulan {$label}",
            'marketing-performa' => "Pesanan oleh {$datasetLabel} - {$label}",
            'divisi-performa' => "Detail Divisi {$label}",
            'status-pie' => "Pesanan Status: {$label}",
            'tipe-pie' => "Pesanan Tipe: {$label}",
            'pendapatan-bar' => "Pendapatan Bulan {$label}",
            default => "Detail Chart: {$label}",
        };

        $orders = collect();

        switch ($chart) {
            case 'pesanan-line':
                $parsed = $this->parseMonthLabel($label);
                if ($parsed) {
                    $orders = Pesanan::whereYear('created_at', $parsed['year'])
                        ->whereMonth('created_at', $parsed['month'])
                        ->with(['user', 'companyInternal'])
                        ->latest()
                        ->get()
                        ->map(fn ($p) => $this->formatOrderRow($p));
                }
                break;

            case 'marketing-performa':
                $user = User::where('name', $datasetLabel)->first();
                $parsed = $this->parseMonthLabel($label);
                if ($user && $parsed) {
                    $orders = Pesanan::where('user_id', $user->id)
                        ->whereYear('created_at', $parsed['year'])
                        ->whereMonth('created_at', $parsed['month'])
                        ->with(['user', 'companyInternal'])
                        ->latest()
                        ->get()
                        ->map(fn ($p) => $this->formatOrderRow($p));
                }
                break;

            case 'divisi-performa':
                $role = match ($label) {
                    'Marketing' => 'marketing',
                    'Finance' => 'finance',
                    'Logistik' => 'logistik',
                    default => strtolower($label),
                };
                // Reuse existing showDivisiDetail logic
                $this->showDivisiDetail($role);
                return;

            case 'status-pie':
                $statusMap = [
                    'Dibuat' => 0, 'Pending' => 1, 'Perlu Rilis Dana' => 2,
                    'Perlu Cetak Invoice' => 3, 'Perlu Penagihan' => 4,
                    'Ditandai Lunas' => 5, 'Cetak Surat Jalan' => 6,
                    'Selesai Dikirim' => 7, 'Selesai' => 8,
                ];
                $statusCode = $statusMap[$label] ?? null;
                if ($statusCode !== null) {
                    $orders = Pesanan::where('status_pesanan', $statusCode)
                        ->with(['user', 'companyInternal'])
                        ->latest()
                        ->get()
                        ->map(fn ($p) => $this->formatOrderRow($p));
                }
                break;

            case 'tipe-pie':
                $tipe = $label === 'Projek' ? 1 : 0;
                $orders = Pesanan::where('tipe_pesanan', $tipe)
                    ->with(['user', 'companyInternal'])
                    ->latest()
                    ->get()
                    ->map(fn ($p) => $this->formatOrderRow($p));
                break;

            case 'pendapatan-bar':
                $parsed = $this->parseMonthLabel($label);
                if ($parsed) {
                    $orders = Pesanan::whereYear('created_at', $parsed['year'])
                        ->whereMonth('created_at', $parsed['month'])
                        ->with(['user', 'companyInternal'])
                        ->latest()
                        ->get()
                        ->map(fn ($p) => [
                            ...$this->formatOrderRow($p),
                            'total_harga' => $p->total_harga,
                            'total_formatted' => 'Rp ' . number_format($p->total_harga ?? 0, 0, ',', '.'),
                        ]);
                }
                break;
        }

        $this->chartDetailData = [
            'title' => $title,
            'chart' => $chart,
            'label' => $label,
            'value' => $value,
            'total' => $orders->count(),
            'orders' => $orders->toArray(),
        ];
        $this->showChartDetailModal = true;
    }

    public function closeChartDetail(): void
    {
        $this->showChartDetailModal = false;
        $this->chartDetailData = null;
    }

    public function showMarketingOverall(): void
    {
        $marketingUsers = User::where('role', 'marketing')
            ->paginate(5, page: $this->marketingOverallPage);
        $months = collect();
        $totals = collect();

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months->push($date->translatedFormat('M Y'));
        }

        $userRows = [];
        $grandTotal = [];
        foreach ($months as $m) {
            $grandTotal[$m] = 0;
        }

        foreach ($marketingUsers as $user) {
            $row = ['name' => $user->name, 'data' => []];
            foreach ($months as $m) {
                $parts = explode(' ', $m);
                $monthStr = $parts[0];
                $year = (int) ($parts[1] ?? now()->year);
                $monthMap = ['Jan'=>1,'Feb'=>2,'Mar'=>3,'Apr'=>4,'Mei'=>5,'Jun'=>6,'Jul'=>7,'Agu'=>8,'Sep'=>9,'Okt'=>10,'Nov'=>11,'Des'=>12];
                $month = $monthMap[$monthStr] ?? 1;

                $count = Pesanan::where('user_id', $user->id)
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->count();
                $row['data'][$m] = $count;
                $grandTotal[$m] += $count;
            }
            $userRows[] = $row;
        }

        $this->marketingOverallData = [
            'months' => $months->toArray(),
            'users' => $userRows,
            'totals' => $grandTotal,
        ];
        $this->marketingOverallPage = $marketingUsers->currentPage();
        $this->marketingOverallLastPage = $marketingUsers->lastPage();
        $this->showMarketingOverallModal = true;
    }

    public function closeMarketingOverall(): void
    {
        $this->showMarketingOverallModal = false;
        $this->marketingOverallData = null;
    }

    protected function parseMonthLabel(string $label): ?array
    {
        $months = [
            'Jan' => 1, 'Feb' => 2, 'Mar' => 3, 'Apr' => 4,
            'Mei' => 5, 'Jun' => 6, 'Jul' => 7, 'Agu' => 8,
            'Sep' => 9, 'Okt' => 10, 'Nov' => 11, 'Des' => 12,
        ];

        // Support both "M Y" and "Month Y" formats
        $parts = explode(' ', $label);
        if (count($parts) >= 2) {
            $monthStr = $parts[0];
            $yearStr = $parts[1] ?? now()->year;
            $month = $months[$monthStr] ?? (int) $monthStr;
            $year = (int) $yearStr;
            if ($month >= 1 && $month <= 12 && $year >= 2000) {
                return ['month' => $month, 'year' => $year];
            }
        }
        return null;
    }

    protected function formatOrderRow(Pesanan $p): array
    {
        return [
            'id' => $p->id,
            'code' => $p->code,
            'company_name' => $p->company_name ?? '-',
            'group_name' => $p->group_name ?? '-',
            'created_by' => $p->user?->name ?? '-',
            'status_label' => match ((int) $p->status_pesanan) {
                0 => 'Dibuat', 1 => 'Pending', 2 => 'Perlu Rilis Dana',
                3 => 'Perlu Cetak Invoice', 4 => 'Perlu Penagihan',
                5 => 'Ditandai Lunas', 6 => 'Cetak Surat Jalan',
                7 => 'Selesai Dikirim', 8 => 'Selesai', default => 'Unknown',
            },
            'created_at' => $p->created_at->format('d M Y'),
        ];
    }

    protected function diffHuman($from, $to): ?array
    {
        if (!$from || !$to) return null;
        try {
            $from = $from instanceof Carbon ? $from : Carbon::parse($from);
            $to = $to instanceof Carbon ? $to : Carbon::parse($to);

            $totalDays = (int) $from->diffInDays($to);

            $years = (int) $from->diffInYears($to);
            if ($years >= 1) {
                $months = (int) $from->diffInMonths($to) % 12;
                $text = $years . ' tahun' . ($months ? ' ' . $months . ' bulan' : '');
                return ['text' => $text, 'totalDays' => $totalDays];
            }

            $months = (int) $from->diffInMonths($to);
            if ($months >= 1) {
                return ['text' => $months . ' bulan', 'totalDays' => $totalDays];
            }

            $days = (int) $from->diffInDays($to);
            if ($days >= 1) {
                return ['text' => $days . ' hari', 'totalDays' => $totalDays];
            }

            $hours = (int) $from->diffInHours($to);
            if ($hours >= 1) {
                $minutes = (int) ($from->diffInMinutes($to) % 60);
                $text = $hours . ' jam' . ($minutes ? ' ' . $minutes . ' menit' : '');
                return ['text' => $text, 'totalDays' => $totalDays];
            }

            $minutes = (int) $from->diffInMinutes($to);
            if ($minutes >= 1) {
                $seconds = (int) ($from->diffInSeconds($to) % 60);
                $text = $minutes . ' menit' . ($seconds ? ' ' . $seconds . ' detik' : '');
                return ['text' => $text, 'totalDays' => $totalDays];
            }

            $seconds = (int) $from->diffInSeconds($to);
            return ['text' => $seconds . ' detik', 'totalDays' => $totalDays];
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function getRoleColor(string $role): string
    {
        return match ($role) {
            'marketing' => 'info',
            'finance' => 'success',
            'logistik' => 'warning',
            default => 'gray',
        };
    }

    protected function loadRoleProgress(): void
    {
        $roles = ['marketing', 'finance', 'logistik'];
        $result = [];
        $dateRange = $this->getFilteredDateRange();

        foreach ($roles as $role) {
            $page = $this->roleProgressPage[$role] ?? 1;
            $pesananIds = Task::where('role', $role)->pluck('pesanan_id')->unique();
            $totalPesanan = Pesanan::whereIn('id', $pesananIds)
                ->when($dateRange, fn ($q) => $q->whereBetween('created_at', $dateRange))
                ->count();
            $completedTasks = Task::where('role', $role)->where('status', 2)->count();
            $totalTasks = Task::where('role', $role)->count();

            $paginator = Pesanan::whereIn('id', $pesananIds)
                ->when($dateRange, fn ($q) => $q->whereBetween('created_at', $dateRange))
                ->with(['tasks' => function ($q) use ($role) {
                    $q->where('role', $role);
                }])
                ->latest()
                ->paginate(5, page: $page);

            $percentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

            $items = [];
            foreach ($paginator->items() as $p) {
                $task = $p->tasks->first();
                $taskStatus = $task ? (int) $task->status : 0;
                $items[] = [
                    'code' => $p->code,
                    'company_name' => $p->company_name ?? '-',
                    'task_status' => $taskStatus,
                    'pesanan_id' => $p->id,
                ];
            }

            $result[] = [
                'role' => ucfirst($role),
                'total' => $totalTasks,
                'completed' => $completedTasks,
                'total_pesanan' => $totalPesanan,
                'percentage' => $percentage,
                'items' => $items,
                'page' => $paginator->currentPage(),
                'lastPage' => $paginator->lastPage(),
            ];
        }

        $this->roleProgressData = $result;
    }

    protected function loadRecentActivities(): void
    {
        $roleColors = [
            'marketing' => 'info',
            'finance' => 'success',
            'logistik' => 'warning',
            'admin' => 'primary',
            'superadmin' => 'danger',
        ];

        $dateRange = $this->getFilteredDateRange();
        $paginator = LogActivities::with('user')
            ->when($dateRange, fn ($q) => $q->whereBetween('created_at', $dateRange))
            ->latest()
            ->paginate(5, page: $this->recentActivitiesPage);

        $this->recentActivities = collect($paginator->items())
            ->map(fn ($log) => [
                'time' => $log->created_at->diffForHumans(),
                'user' => $log->user?->name ?? 'System',
                'role' => ucfirst($log->user?->role ?? 'system'),
                'role_color' => $roleColors[$log->user?->role ?? 'system'] ?? 'gray',
                'action' => $log->action,
                'description' => $log->description,
            ])
            ->toArray();

        $this->recentActivitiesPage = $paginator->currentPage();
        $this->recentActivitiesLastPage = $paginator->lastPage();
    }

    protected function loadRoleTables(): void
    {
        $roles = ['marketing' => 'Marketing', 'finance' => 'Finance', 'logistik' => 'Logistik'];
        $result = [];
        $dateRange = $this->getFilteredDateRange();

        foreach ($roles as $roleKey => $roleLabel) {
            $page = $this->roleTablePage[$roleKey] ?? 1;

            $paginator = Pesanan::whereHas('tasks', fn ($q) => $q->where('role', $roleKey))
                ->when($dateRange, fn ($q) => $q->whereBetween('created_at', $dateRange))
                ->with([
                    'tasks' => fn ($q) => $q->where('role', $roleKey)->with([
                        'taskActivities.createdUser',
                        'taskActivities.updatedUser',
                    ]),
                    'user',
                ])
                ->latest()
                ->paginate(5, page: $page);

            $items = [];
            foreach ($paginator->items() as $order) {
                $task = $order->tasks->first();
                $taskStatus = $task ? (int) $task->status : 0;
                $statusLabel = $taskStatus === 2 ? 'Selesai' : ($taskStatus === 1 ? 'Proses' : 'Pending');

                $users = collect();
                if ($task) {
                    foreach ($task->taskActivities as $activity) {
                        if ($activity->createdUser) $users->push($activity->createdUser->name);
                        if ($activity->updatedUser) $users->push($activity->updatedUser->name);
                    }
                }
                $involvedUsers = $users->unique()->values()->toArray();

                $items[] = [
                    'no_po' => $order->no_po ?: $order->code,
                    'company' => $order->company_name ?? '-',
                    'created_by' => $order->user?->name ?? '-',
                    'involved_users' => $involvedUsers,
                    'status' => $statusLabel,
                    'status_code' => $taskStatus,
                    'pesanan_id' => $order->id,
                ];
            }

            $result[$roleKey] = [
                'label' => $roleLabel,
                'items' => $items,
                'count' => $paginator->total(),
            ];

            $this->roleTablePage[$roleKey] = $paginator->currentPage();
            $this->roleTableLastPage[$roleKey] = $paginator->lastPage();
        }

        $this->roleTables = $result;
    }

    protected function loadUnparticipatedUsers(): void
    {
        $roles = ['marketing', 'finance', 'logistik'];
        $result = [];

        foreach ($roles as $role) {
            $allUsers = User::where('role', $role)->pluck('name', 'id');

            $taskIds = Task::where('role', $role)->pluck('id');

            $participatedUserIds = collect();
            if ($taskIds->isNotEmpty()) {
                $participatedUserIds = TaskActivity::whereIn('task_id', $taskIds)
                    ->where(function ($q) {
                        $q->whereNotNull('created_user_id')
                          ->orWhereNotNull('updated_user_id');
                    })
                    ->get()
                    ->flatMap(fn ($a) => [$a->created_user_id, $a->updated_user_id])
                    ->unique()
                    ->filter();
            }

            $unparticipated = $allUsers->filter(fn ($name, $id) => !$participatedUserIds->contains($id));

            $result[$role] = [
                'role_label' => ucfirst($role),
                'total' => $allUsers->count(),
                'participated' => $allUsers->count() - $unparticipated->count(),
                'users' => $unparticipated->map(fn ($name, $id) => [
                    'id' => $id,
                    'name' => $name,
                ])->values()->toArray(),
            ];
        }

        $this->unparticipatedUsers = $result;
    }

    public function viewPesanan(array $arguments): void
    {
        $pesananId = $arguments['pesanan_id'] ?? null;
        if (!$pesananId) return;

        $this->redirect(route('filament.admin.resources.pemesanans.view', $pesananId));
    }

    public static function getNavigationLabel(): string
    {
        return 'Dashboard';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-home';
    }

}
