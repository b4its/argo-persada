<?php

namespace App\Filament\Pages\Admin;

use App\Models\Pesanan;
use App\Models\Task;
use App\Models\LogActivities;
use Carbon\Carbon;
use Filament\Pages\Page;

class AdminDashboard extends Page
{
    protected static ?string $title = 'Dashboard Admin';

    protected string $view = 'filament.pages.admin.admin-dashboard';

    public array $roleProgressData = [];
    public array $recentActivities = [];
    public array $roleTables = [];
    public ?array $detailData = null;
    public bool $showDetailModal = false;

    public function mount(): void
    {
        $this->loadRoleProgress();
        $this->loadRecentActivities();
        $this->loadRoleTables();
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
            'tanggal_rilis_dana' => $pesanan->tanggal_rilis_dana ? Carbon::parse($pesanan->tanggal_rilis_dana)->format('d M Y') : '-',
            'tanggal_terbit_invoice' => $pesanan->tanggal_terbit_invoice ? Carbon::parse($pesanan->tanggal_terbit_invoice)->format('d M Y') : '-',
            'tanggal_jatuh_tempo' => $pesanan->tanggal_jatuh_tempo ? Carbon::parse($pesanan->tanggal_jatuh_tempo)->format('d M Y') : '-',
            'tanggal_terbit_surat_jalan' => $pesanan->tanggal_terbit_surat_jalan ? Carbon::parse($pesanan->tanggal_terbit_surat_jalan)->format('d M Y') : '-',
            'tanggal_surat_kembali' => $pesanan->tanggal_surat_kembali ? Carbon::parse($pesanan->tanggal_surat_kembali)->format('d M Y') : '-',
            'tanggal_lunas' => $pesanan->tanggal_lunas ? Carbon::parse($pesanan->tanggal_lunas)->format('d M Y') : '-',

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

    protected function loadRoleProgress(): void
    {
        $roles = ['marketing', 'finance', 'logistik'];
        $result = [];

        foreach ($roles as $role) {
            $pesananIds = Task::where('role', $role)->pluck('pesanan_id')->unique();
            $totalPesanan = Pesanan::whereIn('id', $pesananIds)->count();
            $completedTasks = Task::where('role', $role)->where('status', 2)->count();
            $totalTasks = Task::where('role', $role)->count();

            $paginator = Pesanan::whereIn('id', $pesananIds)
                ->with(['tasks' => function ($q) use ($role) {
                    $q->where('role', $role);
                }])
                ->latest()
                ->paginate(5);

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

        $this->recentActivities = LogActivities::with('user')
            ->latest()
            ->take(20)
            ->get()
            ->map(fn ($log) => [
                'time' => $log->created_at->diffForHumans(),
                'user' => $log->user?->name ?? 'System',
                'role' => ucfirst($log->user?->role ?? 'system'),
                'role_color' => $roleColors[$log->user?->role ?? 'system'] ?? 'gray',
                'action' => $log->action,
                'description' => $log->description,
            ])
            ->toArray();
    }

    protected function loadRoleTables(): void
    {
        $roles = ['marketing' => 'Marketing', 'finance' => 'Finance', 'logistik' => 'Logistik'];
        $result = [];

        foreach ($roles as $roleKey => $roleLabel) {
            $orders = Pesanan::whereHas('tasks', fn ($q) => $q->where('role', $roleKey))
                ->with([
                    'tasks' => fn ($q) => $q->where('role', $roleKey)->with([
                        'taskActivities.createdUser',
                        'taskActivities.updatedUser',
                    ]),
                    'user',
                ])
                ->latest()
                ->take(10)
                ->get();

            $items = [];
            foreach ($orders as $order) {
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
                'count' => count($items),
            ];
        }

        $this->roleTables = $result;
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
