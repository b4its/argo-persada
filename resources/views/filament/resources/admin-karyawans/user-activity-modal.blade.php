@php
    $userId = $user->id;

    // Total pesanan dibuat oleh user
    $totalPesananDibuat = \App\Models\Pesanan::where('user_id', $userId)->count();

    // Semua task_activity di mana user berpartisipasi
    $taskActivityIds = \App\Models\TaskActivity::where(function ($q) use ($userId) {
        $q->where('created_user_id', $userId)->orWhere('updated_user_id', $userId);
    })->pluck('task_id');

    // Total task unik yang dikerjakan user
    $totalTaskDikerjakan = $taskActivityIds->unique()->count();

    // Total task selesai oleh user
    $totalTaskSelesai = \App\Models\Task::whereIn('id', $taskActivityIds->unique())
        ->where('status', 2)->count();

    // Pesanan terkait user (dibuat + memiliki task dengan aktivitas user)
    $pesananViaTask = \App\Models\Pesanan::whereHas('tasks', function ($q) use ($taskActivityIds) {
        $q->whereIn('id', $taskActivityIds->unique());
    })->pluck('id');

    $pesananIds = \App\Models\Pesanan::where('user_id', $userId)
        ->orWhereIn('id', $pesananViaTask)
        ->orderBy('created_at', 'desc')
        ->take(50)
        ->pluck('id');

    $pesananList = \App\Models\Pesanan::whereIn('id', $pesananIds)
        ->with('user')
        ->orderBy('created_at', 'desc')
        ->get();

    // Aktivitas terbaru
    $recentActivities = \App\Models\TaskActivity::where(function ($q) use ($userId) {
        $q->where('created_user_id', $userId)->orWhere('updated_user_id', $userId);
    })
    ->with(['task.pesanan', 'createdUser', 'updatedUser'])
    ->latest()
    ->take(50)
    ->get();

    $totalPesananTerkait = \App\Models\Pesanan::where('user_id', $userId)
        ->orWhereIn('id', $pesananViaTask)->count();
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

    {{-- Aktivitas Terbaru --}}
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
                <button @click="page = Math.max(1, page - 1)" :disabled="page === 1"
                        class="text-xs font-medium text-primary-600 hover:text-primary-500 disabled:text-gray-400 disabled:cursor-not-allowed transition">
                    &laquo;
                </button>
                <span class="text-xs text-gray-500" x-text="`${page} / ${Math.ceil(total / perPage)}`"></span>
                <button @click="page = Math.min(Math.ceil(total / perPage), page + 1)" :disabled="page === Math.ceil(total / perPage)"
                        class="text-xs font-medium text-primary-600 hover:text-primary-500 disabled:text-gray-400 disabled:cursor-not-allowed transition">
                    &raquo;
                </button>
            </div>
        @endif
    </div>

    {{-- Daftar Pesanan --}}
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
                <button @click="page = Math.max(1, page - 1)" :disabled="page === 1"
                        class="text-xs font-medium text-primary-600 hover:text-primary-500 disabled:text-gray-400 disabled:cursor-not-allowed transition">
                    &laquo;
                </button>
                <span class="text-xs text-gray-500" x-text="`${page} / ${Math.ceil(total / perPage)}`"></span>
                <button @click="page = Math.min(Math.ceil(total / perPage), page + 1)" :disabled="page === Math.ceil(total / perPage)"
                        class="text-xs font-medium text-primary-600 hover:text-primary-500 disabled:text-gray-400 disabled:cursor-not-allowed transition">
                    &raquo;
                </button>
            </div>
        @endif
    </div>

</div>
