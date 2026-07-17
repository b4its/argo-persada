@php
    use App\Models\Pesanan;
    use App\Models\Task;
    use App\Models\TaskActivity;
    use Carbon\Carbon;

    $userId = $user->id;

    $totalPesananDibuat = Pesanan::where('user_id', $userId)->count();

    $taskActivityIds = TaskActivity::where(function ($q) use ($userId) {
        $q->where('created_user_id', $userId)->orWhere('updated_user_id', $userId);
    })->pluck('task_id');

    $totalTaskDikerjakan = $taskActivityIds->unique()->count();

    $totalTaskSelesai = Task::whereIn('id', $taskActivityIds->unique())
        ->where('status', 2)->count();

    $pesananViaTask = Pesanan::whereHas('tasks', function ($q) use ($taskActivityIds) {
        $q->whereIn('id', $taskActivityIds->unique());
    })->pluck('id');

    $pesananIds = Pesanan::where('user_id', $userId)
        ->orWhereIn('id', $pesananViaTask)
        ->orderBy('created_at', 'desc')
        ->take(50)
        ->pluck('id');

    $pesananList = Pesanan::whereIn('id', $pesananIds)
        ->with('user')
        ->orderBy('created_at', 'desc')
        ->get();

    $recentActivities = TaskActivity::where(function ($q) use ($userId) {
        $q->where('created_user_id', $userId)->orWhere('updated_user_id', $userId);
    })
    ->with(['task.pesanan', 'createdUser', 'updatedUser'])
    ->latest()
    ->take(50)
    ->get();

    $totalPesananTerkait = Pesanan::where('user_id', $userId)
        ->orWhereIn('id', $pesananViaTask)->count();

    // Batas Waktu Computation
    $userTasks = Task::whereIn('id', $taskActivityIds->unique())->get();
    $now = Carbon::now();
    $taskRows = [];
    $stats = ['Tepat Waktu' => 0, 'Terlambat' => 0, 'Dalam Proses' => 0];

    foreach ($userTasks as $task) {
        $hoursSinceCreation = (int) $task->created_at->diffInHours($now);

        if ($task->status === 2) {
            $hoursToComplete = (int) $task->created_at->diffInHours($task->updated_at);
            $label = $hoursToComplete <= 48 ? 'Tepat Waktu' : 'Terlambat';
            $duration = $hoursToComplete . ' jam';
            $completedAt = $task->updated_at->format('d M Y H:i');
        } elseif ($task->status === 1) {
            $label = $hoursSinceCreation <= 48 ? 'Dalam Proses' : 'Terlambat';
            $duration = $hoursSinceCreation . ' jam';
            $completedAt = '-';
        } else {
            $label = 'Dalam Proses';
            $duration = $hoursSinceCreation . ' jam';
            $completedAt = '-';
        }

        if (isset($stats[$label])) $stats[$label]++;

        $taskRows[] = [
            'pesanan_code' => $task->pesanan?->code ?? '-',
            'role' => $task->role,
            'status' => (int) $task->status,
            'batas_label' => $label,
            'created_at' => $task->created_at->format('d M Y H:i'),
            'completed_at' => $completedAt,
            'duration' => $duration,
        ];
    }
@endphp

<div class="space-y-6">

    {{-- Rangkuman --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="rounded-lg bg-primary-50 dark:bg-primary-900/20 p-4 text-center">
            <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ $totalPesananDibuat }}</p>
            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Pesanan Dibuat</p>
        </div>
        <div class="rounded-lg bg-info-50 dark:bg-info-900/20 p-4 text-center">
            <p class="text-2xl font-bold text-info-600 dark:text-info-400">{{ $totalPesananTerkait }}</p>
            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Total Pesanan Terkait</p>
        </div>
        <div class="rounded-lg bg-warning-50 dark:bg-warning-900/20 p-4 text-center">
            <p class="text-2xl font-bold text-warning-600 dark:text-warning-400">{{ $totalTaskDikerjakan }}</p>
            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Task Dikerjakan</p>
        </div>
        <div class="rounded-lg bg-success-50 dark:bg-success-900/20 p-4 text-center">
            <p class="text-2xl font-bold text-success-600 dark:text-success-400">{{ $totalTaskSelesai }}</p>
            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Task Selesai</p>
        </div>
    </div>

    {{-- Batas Waktu Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        @foreach($stats as $label => $count)
            @php
                $cardColor = match($label) {
                    'Tepat Waktu' => 'text-success-600',
                    'Terlambat' => 'text-warning-600',
                    'Dalam Proses' => 'text-primary-600',
                    default => 'text-gray-600',
                };
            @endphp
            <div class="rounded-lg border border-gray-200 dark:border-white/10 p-3 text-center">
                <span class="text-2xl font-bold {{ $cardColor }}">{{ $count }}</span>
                <p class="text-xs text-gray-500 mt-1">{{ $label }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4"
         x-init="
            $nextTick(() => {
                if (typeof Chart === 'undefined') return;
                var bc = document.getElementById('karyawanBarChart');
                var pc = document.getElementById('karyawanPieChart');
                if (!bc && !pc) return;
                var labels = {{ json_encode(array_keys($stats)) }};
                var vals = {{ json_encode(array_values($stats)) }};
                if (bc) {
                    new Chart(bc.getContext('2d'), {
                        type: 'bar',
                        data: { labels: labels, datasets: [{ label: 'Jumlah Tugas', data: vals, backgroundColor: ['#10b981','#ef4444','#3b82f6'] }] },
                        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { callbacks: { label: function(ctx) { return ctx.parsed.y + ' tugas'; } } } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
                    });
                }
                if (pc) {
                    new Chart(pc.getContext('2d'), {
                        type: 'pie',
                        data: { labels: labels, datasets: [{ data: vals, backgroundColor: ['#10b981','#ef4444','#3b82f6'] }] },
                        options: { responsive: true, plugins: { legend: { position: 'right', labels: { boxWidth: 12, padding: 8, font: { size: 11 } } } } }
                    });
                }
            });
         ">
        <div class="rounded-lg border border-gray-200 dark:border-white/10 p-4">
            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 uppercase tracking-wide">Grafik Batang</h4>
            <div class="max-h-44"><canvas id="karyawanBarChart" height="50"></canvas></div>
        </div>
        <div class="rounded-lg border border-gray-200 dark:border-white/10 p-4">
            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 uppercase tracking-wide">Grafik Lingkaran</h4>
            <div class="max-h-44"><canvas id="karyawanPieChart" height="50"></canvas></div>
        </div>
    </div>

    <div x-data="{ page: 1, perPage: 5, total: {{ count($taskRows) }} }">
        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 uppercase tracking-wide">
            Daftar Tugas ({{ count($taskRows) }})
        </h4>
        <div class="overflow-x-auto max-h-72 overflow-y-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-white/10">
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">No. Pesanan</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Divisi</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Batas Waktu</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Durasi</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Dibuat</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Selesai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                    @forelse($taskRows as $i => $t)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition"
                            x-show="Math.ceil(({{ $i }} + 1) / perPage) === page"
                            style="display: none">
                            <td class="px-3 py-2 font-medium text-gray-900 dark:text-white whitespace-nowrap">{{ $t['pesanan_code'] }}</td>
                            <td class="px-3 py-2 text-center text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ ucfirst($t['role']) }}</td>
                            <td class="px-3 py-2 text-center whitespace-nowrap">
                                @php
                                    $sc = match($t['status']) { 2 => 'success', 1 => 'warning', default => 'gray' };
                                    $sl = match($t['status']) { 2 => 'Selesai', 1 => 'Proses', default => 'Pending' };
                                @endphp
                                <x-filament::badge color="{{ $sc }}" size="sm">{{ $sl }}</x-filament::badge>
                            </td>
                            <td class="px-3 py-2 text-center whitespace-nowrap">
                                @php
                                    $bc = match($t['batas_label']) {
                                        'Tepat Waktu' => 'success',
                                        'Terlambat' => 'danger',
                                        'Dalam Proses' => 'warning',
                                        default => 'gray',
                                    };
                                @endphp
                                <x-filament::badge color="{{ $bc }}" size="sm">{{ $t['batas_label'] }}</x-filament::badge>
                            </td>
                            <td class="px-3 py-2 text-right text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $t['duration'] }}</td>
                            <td class="px-3 py-2 text-right text-gray-500 dark:text-gray-400 whitespace-nowrap text-xs">{{ $t['created_at'] }}</td>
                            <td class="px-3 py-2 text-right text-gray-500 dark:text-gray-400 whitespace-nowrap text-xs">{{ $t['completed_at'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-4 text-center text-sm text-gray-500">Tidak ada tugas</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(count($taskRows) > 5)
            <div class="flex items-center justify-between pt-3 mt-3 border-t border-gray-200 dark:border-white/10">
                <button type="button" @click.prevent="page = Math.max(1, page - 1)" :disabled="page === 1"
                        class="text-xs font-medium text-primary-600 hover:text-primary-500 disabled:text-gray-400 disabled:cursor-not-allowed transition">
                    &laquo;
                </button>
                <span class="text-xs text-gray-500" x-text="`${page} / ${Math.ceil(total / perPage)}`"></span>
                <button type="button" @click.prevent="page = Math.min(Math.ceil(total / perPage), page + 1)" :disabled="page === Math.ceil(total / perPage)"
                        class="text-xs font-medium text-primary-600 hover:text-primary-500 disabled:text-gray-400 disabled:cursor-not-allowed transition">
                    &raquo;
                </button>
            </div>
        @endif
    </div>

    <div x-data="{ page: 1, perPage: 5, total: {{ count($recentActivities) }} }">
        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 uppercase tracking-wide">Aktivitas Terbaru</h4>
        <div class="space-y-2 max-h-60 overflow-y-auto">
            @forelse($recentActivities as $i => $act)
                <div class="flex items-start gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 transition"
                     x-show="Math.ceil(({{ $i }} + 1) / perPage) === page"
                     style="display: none">
                    <div class="w-2 h-2 mt-2 rounded-full flex-shrink-0
                        @if($act->created_user_id === $userId) bg-success-500 @else bg-info-500 @endif">
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm text-gray-900 dark:text-white">
                            <span class="font-medium">{{ $act->createdUser?->name ?? $act->updatedUser?->name ?? 'System' }}</span>
                            @if($act->task?->pesanan)
                                <span class="text-gray-500">pada</span>
                                <span class="font-medium">{{ $act->task->pesanan->code }}</span>
                            @endif
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            {{ $act->note ?: 'Tidak ada catatan' }}
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $act->created_at->format('d M Y H:i') }}
                            @if($act->created_user_id === $userId)
                                <x-filament::badge color="success" size="xs">Creator</x-filament::badge>
                            @endif
                            @if($act->updated_user_id === $userId)
                                <x-filament::badge color="info" size="xs">Updater</x-filament::badge>
                            @endif
                        </p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500 text-center py-4">Belum ada aktivitas</p>
            @endforelse
        </div>
        @if(count($recentActivities) > 5)
            <div class="flex items-center justify-between pt-3 mt-3 border-t border-gray-200 dark:border-white/10">
                <button type="button" @click.prevent="page = Math.max(1, page - 1)" :disabled="page === 1"
                        class="text-xs font-medium text-primary-600 hover:text-primary-500 disabled:text-gray-400 disabled:cursor-not-allowed transition">
                    &laquo;
                </button>
                <span class="text-xs text-gray-500" x-text="`${page} / ${Math.ceil(total / perPage)}`"></span>
                <button type="button" @click.prevent="page = Math.min(Math.ceil(total / perPage), page + 1)" :disabled="page === Math.ceil(total / perPage)"
                        class="text-xs font-medium text-primary-600 hover:text-primary-500 disabled:text-gray-400 disabled:cursor-not-allowed transition">
                    &raquo;
                </button>
            </div>
        @endif
    </div>

    <div x-data="{ page: 1, perPage: 5, total: {{ count($pesananList) }} }">
        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 uppercase tracking-wide">Pesanan Terkait</h4>
        <div class="overflow-x-auto max-h-72 overflow-y-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-white/10">
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Perusahaan</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dibuat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                    @forelse($pesananList as $i => $p)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition"
                            x-show="Math.ceil(({{ $i }} + 1) / perPage) === page"
                            style="display: none">
                            <td class="px-3 py-2 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                {{ $p->code }}
                            </td>
                            <td class="px-3 py-2 text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                {{ $p->company_name ?? '-' }}
                            </td>
                            <td class="px-3 py-2 text-center text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                Rp {{ number_format($p->total_harga ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-3 py-2 text-center whitespace-nowrap">
                                @php
                                    $st = (int) ($p->status_pesanan ?? 0);
                                    $statusLabels = ['Dibuat','Pending','Perlu Rilis Dana','Perlu Cetak Invoice','Perlu Penagihan','Ditandai Lunas','Cetak Surat Jalan','Selesai Dikirim','Selesai'];
                                    $statusColors = ['gray','warning','danger','info','info','success','warning','success','success'];
                                    $label = $statusLabels[$st] ?? 'Unknown';
                                    $color = $statusColors[$st] ?? 'gray';
                                @endphp
                                <x-filament::badge color="{{ $color }}" size="sm">{{ $label }}</x-filament::badge>
                            </td>
                            <td class="px-3 py-2 text-gray-500 dark:text-gray-400 whitespace-nowrap text-xs">
                                {{ $p->created_at->format('d M Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-4 text-center text-sm text-gray-500">
                                Tidak ada pesanan terkait
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(count($pesananList) > 5)
            <div class="flex items-center justify-between pt-3 mt-3 border-t border-gray-200 dark:border-white/10">
                <button type="button" @click.prevent="page = Math.max(1, page - 1)" :disabled="page === 1"
                        class="text-xs font-medium text-primary-600 hover:text-primary-500 disabled:text-gray-400 disabled:cursor-not-allowed transition">
                    &laquo;
                </button>
                <span class="text-xs text-gray-500" x-text="`${page} / ${Math.ceil(total / perPage)}`"></span>
                <button type="button" @click.prevent="page = Math.min(Math.ceil(total / perPage), page + 1)" :disabled="page === Math.ceil(total / perPage)"
                        class="text-xs font-medium text-primary-600 hover:text-primary-500 disabled:text-gray-400 disabled:cursor-not-allowed transition">
                    &raquo;
                </button>
            </div>
        @endif
    </div>

</div>