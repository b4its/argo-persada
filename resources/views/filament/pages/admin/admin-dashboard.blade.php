<x-filament-panels::page>

    <div class="space-y-6">

        {{-- ── BACKUP / IMPORT DATABASE ── --}}
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Database</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Backup atau import database (.sql)</p>
                </div>
                <div class="flex items-center gap-2">
                    <x-filament::button color="warning" icon="heroicon-m-arrow-down-tray" tag="a" href="/tool-backup" target="_blank">
                        Download Backup
                    </x-filament::button>
                    <x-filament::button color="danger" icon="heroicon-m-arrow-up-tray" tag="a" href="/tool-import">
                        Import SQL
                    </x-filament::button>
                </div>
            </div>
        </div>

        {{-- ── ROLE PROGRESS SECTION ── --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            @foreach($roleProgressData as $data)
                <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ $data['role'] }}
                        </h3>
                        <x-filament::badge color="{{ $data['percentage'] >= 100 ? 'success' : ($data['percentage'] > 0 ? 'warning' : 'gray') }}" size="lg">
                            {{ $data['percentage'] }}%
                        </x-filament::badge>
                    </div>

                    {{-- Progress Bar --}}
                    <div class="w-full bg-gray-200 rounded-full h-2.5 mb-4 dark:bg-gray-700">
                        <div class="bg-{{ $data['percentage'] >= 100 ? 'success' : ($data['percentage'] > 0 ? 'warning' : 'gray') }}-500 h-2.5 rounded-full transition-all duration-500"
                             style="width: {{ $data['percentage'] }}%">
                        </div>
                    </div>

                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-4">
                        <span>{{ $data['completed'] }} / {{ $data['total'] }} Tugas Selesai</span>
                        <span>{{ $data['total_pesanan'] }} Pesanan</span>
                    </div>

                    {{-- Daftar Pesanan per Role --}}
                    <div class="space-y-2 max-h-60 overflow-y-auto">
                        @forelse($data['items'] as $p)
                            <div class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 transition">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ $p['code'] }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                        {{ $p['company_name'] }}
                                    </p>
                                </div>
                                <div class="ml-2 flex-shrink-0 flex items-center gap-2">
                                    @php
                                        $st = $p['task_status'];
                                        $statusColor = $st === 2 ? 'success' : ($st === 1 ? 'warning' : 'gray');
                                        $statusLabel = $st === 2 ? 'Selesai' : ($st === 1 ? 'Proses' : 'Pending');
                                    @endphp
                                    <x-filament::badge color="{{ $statusColor }}" size="sm">
                                        {{ $statusLabel }}
                                    </x-filament::badge>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 text-center py-4">Tidak ada pesanan</p>
                        @endforelse
                    </div>

                    @if($data['lastPage'] > 1)
                        <div class="flex items-center justify-between pt-2">
                            <x-filament::button
                                color="gray" size="xs"
                                wire:click="setRoleProgressPage('{{ strtolower($data['role']) }}', {{ $data['page'] - 1 }})"
                                :disabled="$data['page'] <= 1"
                            >
                                &laquo;
                            </x-filament::button>
                            <span class="text-xs text-gray-500">
                                {{ $data['page'] }} / {{ $data['lastPage'] }}
                            </span>
                            <x-filament::button
                                color="gray" size="xs"
                                wire:click="setRoleProgressPage('{{ strtolower($data['role']) }}', {{ $data['page'] + 1 }})"
                                :disabled="$data['page'] >= $data['lastPage']"
                            >
                                &raquo;
                            </x-filament::button>
                        </div>
                    @endif

                    {{-- Tombol Detail Divisi --}}
                    <div class="mt-4 pt-3 border-t border-gray-100 dark:border-white/10">
                        <x-filament::button
                            color="{{ strtolower($data['role']) === 'marketing' ? 'info' : (strtolower($data['role']) === 'finance' ? 'success' : 'warning') }}"
                            size="xs"
                            icon="heroicon-m-eye"
                            wire:click="showDivisiDetail('{{ strtolower($data['role']) }}')"
                        >
                            Detail {{ $data['role'] }}
                        </x-filament::button>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ── USER TIDAK BERPARTISIPASI ── --}}
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-white/10">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                    User Tidak Berpartisipasi
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">
                    User pada setiap divisi yang belum pernah terlibat dalam task/pesanan
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4">
                @foreach($unparticipatedUsers as $roleKey => $data)
                    <div class="rounded-lg border border-gray-200 dark:border-white/10 p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white uppercase">
                                {{ $data['role_label'] }}
                            </h4>
                            <x-filament::badge
                                color="{{ $data['users'] ? 'danger' : 'success' }}"
                                size="sm"
                            >
                                {{ $data['participated'] }}/{{ $data['total'] }} Aktif
                            </x-filament::badge>
                        </div>
                        @if($data['users'])
                            <ul class="space-y-1">
                                @foreach($data['users'] as $user)
                                    <li class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                        <x-heroicon-m-user class="w-4 h-4 text-danger-400" />
                                        {{ $user['name'] }}
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-success-600 dark:text-success-400">
                                Semua user aktif
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ── RECENT ACTIVITIES ── --}}
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-white/10">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                    Aktifitas Terbaru
                </h3>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-white/5">
                @forelse($recentActivities as $activity)
                    <div class="px-6 py-3 hover:bg-gray-50 dark:hover:bg-white/5 transition">
                        <div class="flex items-start gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $activity['user'] }}
                                    </span>
                                    <x-filament::badge color="{{ $activity['role_color'] }}" size="sm">
                                        {{ $activity['role'] }}
                                    </x-filament::badge>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $activity['time'] }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">
                                    <span class="font-medium">{{ $activity['action'] }}</span>
                                    {{ $activity['description'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-sm text-gray-500">
                        Belum ada aktivitas
                    </div>
                @endforelse
                @if($recentActivitiesLastPage > 1)
                    <div class="flex items-center justify-between px-6 py-3 border-t border-gray-200 dark:border-white/10">
                        <x-filament::button
                            color="gray" size="xs"
                            wire:click="setRecentActivitiesPage({{ $recentActivitiesPage - 1 }})"
                            :disabled="$recentActivitiesPage <= 1"
                        >
                            &laquo; Sebelumnya
                        </x-filament::button>
                        <span class="text-xs text-gray-500">
                            Halaman {{ $recentActivitiesPage }} dari {{ $recentActivitiesLastPage }}
                        </span>
                        <x-filament::button
                            color="gray" size="xs"
                            wire:click="setRecentActivitiesPage({{ $recentActivitiesPage + 1 }})"
                            :disabled="$recentActivitiesPage >= $recentActivitiesLastPage"
                        >
                            Selanjutnya &raquo;
                        </x-filament::button>
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ── TABEL PER ROLE (PIC & KETERLIBATAN) ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-6">
        @foreach($roleTables as $roleKey => $table)
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-white/10 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ $table['label'] }}
                    </h3>
                    <x-filament::badge color="gray" size="sm">
                        {{ $table['count'] }} Pesanan
                    </x-filament::badge>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-white/10">
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">No. PO</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Perusahaan</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">PIC</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                            @forelse($table['items'] as $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition">
                                    <td class="px-3 py-2 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                        {{ $item['no_po'] }}
                                    </td>
                                    <td class="px-3 py-2 text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                        {{ $item['company'] }}
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="flex flex-wrap gap-1">
                                            @forelse($item['involved_users'] as $user)
                                                <x-filament::badge color="gray" size="sm">
                                                    {{ $user }}
                                                </x-filament::badge>
                                            @empty
                                                <span class="text-xs text-gray-400">-</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        @php
                                            $st = $item['status_code'];
                                            $sc = $st === 2 ? 'success' : ($st === 1 ? 'warning' : 'gray');
                                        @endphp
                                        <x-filament::badge color="{{ $sc }}" size="sm">
                                            {{ $item['status'] }}
                                        </x-filament::badge>
                                    </td>
                                    <td class="px-3 py-2 text-center whitespace-nowrap">
                                        <x-filament::button
                                            color="info"
                                            size="xs"
                                            icon="heroicon-m-eye"
                                            wire:click="$wire.showDetail({{ $item['pesanan_id'] }})"
                                        >
                                            Detail
                                        </x-filament::button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-4 text-center text-sm text-gray-500">
                                        Belum ada pesanan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($roleTableLastPage[$roleKey] > 1)
                    <div class="flex items-center justify-between px-4 py-2 border-t border-gray-200 dark:border-white/10">
                        <x-filament::button
                            color="gray" size="xs"
                            wire:click="setRoleTablePage('{{ $roleKey }}', {{ $roleTablePage[$roleKey] - 1 }})"
                            :disabled="$roleTablePage[$roleKey] <= 1"
                        >
                            &laquo;
                        </x-filament::button>
                        <span class="text-xs text-gray-500">
                            {{ $roleTablePage[$roleKey] }} / {{ $roleTableLastPage[$roleKey] }}
                        </span>
                        <x-filament::button
                            color="gray" size="xs"
                            wire:click="setRoleTablePage('{{ $roleKey }}', {{ $roleTablePage[$roleKey] + 1 }})"
                            :disabled="$roleTablePage[$roleKey] >= $roleTableLastPage[$roleKey]"
                        >
                            &raquo;
                        </x-filament::button>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- ── MODAL DETAIL PESANAN ── --}}
    <div
        x-data="{ open: @entangle('showDetailModal') }"
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 flex items-start justify-center pt-10 pb-10 overflow-y-auto"
        x-on:keydown.escape.window="open = false; $wire.closeDetail()"
    >
        {{-- Overlay --}}
        <div x-show="open" x-cloak x-transition.opacity
             class="fixed inset-0 bg-gray-900/50 dark:bg-gray-900/80 backdrop-blur-sm"
             x-on:click="open = false; $wire.closeDetail()">
        </div>

        {{-- Modal Content --}}
        <div x-show="open" x-cloak x-transition
             class="relative w-full max-w-5xl bg-white dark:bg-gray-900 rounded-xl shadow-2xl ring-1 ring-gray-950/5 dark:ring-white/10 mx-4">
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-white/10">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Detail Pesanan: {{ $detailData['code'] ?? '' }}
                    </h3>
                    <p class="text-sm text-gray-500 mt-0.5">
                        Informasi pesanan dan keterlibatan seluruh divisi
                    </p>
                </div>
                <button x-on:click="open = false; $wire.closeDetail()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                    <x-heroicon-m-x-mark class="w-6 h-6" />
                </button>
            </div>

            {{-- Body --}}
            @if($detailData)
            <div class="px-6 py-4 space-y-6 max-h-[70vh] overflow-y-auto">

                {{-- 1. INFO UMUM PESANAN --}}
                <div class="rounded-lg border border-gray-200 dark:border-white/10 p-4">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 uppercase tracking-wide">Informasi Umum</h4>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div>
                            <span class="text-xs text-gray-500 block">No. Pesanan</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $detailData['code'] }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">No. PO</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $detailData['no_po'] }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">No. Requisition</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $detailData['no_requisition'] }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">No. Invoice</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $detailData['no_invoice'] }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">No. Delivery Order</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $detailData['no_delivery_order'] }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">Jarak PO → DO</span>
                            <span class="text-sm font-semibold">
                                @php $podiff = $detailData['po_to_do_diff']; @endphp
                                @if($podiff !== null)
                                    <x-filament::badge color="{{ ($podiff['totalDays'] ?? 0) > 30 ? 'danger' : (($podiff['totalDays'] ?? 0) > 14 ? 'warning' : 'success') }}" size="sm">
                                        {{ $podiff['text'] }}
                                    </x-filament::badge>
                                @else
                                    <span class="text-gray-400">Pesanan belum sampai di Logistik</span>
                                @endif
                            </span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">Group</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $detailData['group_name'] }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">Perusahaan</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $detailData['company'] }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">Alamat</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $detailData['address'] }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">Dibuat Oleh</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $detailData['created_by'] }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">Dibuat Pada</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $detailData['created_at'] }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">Status</span>
                            <span class="text-sm font-medium">
                                <x-filament::badge>{{ $detailData['status'] }}</x-filament::badge>
                            </span>
                        </div>
                    </div>
                </div>

                {{-- 2. KEUANGAN --}}
                <div class="rounded-lg border border-gray-200 dark:border-white/10 p-4">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 uppercase tracking-wide">Informasi Keuangan</h4>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div>
                            <span class="text-xs text-gray-500 block">Total Harga</span>
                            <span class="text-sm font-semibold text-success-600 dark:text-success-400">{{ $detailData['total_formatted'] }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">PPN</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $detailData['ppn'] }}%</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">Metode Rilis Dana</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $detailData['metode_rilis'] }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">Status Rilis Dana</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $detailData['status_rilis'] }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">Metode Pembayaran</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $detailData['metode_lunas'] }}</span>
                        </div>
                    </div>
                </div>

                {{-- 3. TANGGAL-TANGGAL --}}
                <div class="rounded-lg border border-gray-200 dark:border-white/10 p-4">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 uppercase tracking-wide">Tanggal-tanggal</h4>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div>
                            <span class="text-xs text-gray-500 block">Rilis Dana</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $detailData['tanggal_rilis_dana'] }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">Terbit Invoice</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $detailData['tanggal_terbit_invoice'] }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">Jatuh Tempo</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $detailData['tanggal_jatuh_tempo'] }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">Terbit Surat Jalan</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $detailData['tanggal_terbit_surat_jalan'] }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">Surat Kembali</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $detailData['tanggal_surat_kembali'] }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">Tanggal Lunas</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $detailData['tanggal_lunas'] }}</span>
                        </div>
                    </div>

                    {{-- Jarak Antar Tahapan --}}
                    <div class="mt-4 pt-3 border-t border-gray-100 dark:border-white/10">
                        <h5 class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wide">Jarak Antar Tahapan</h5>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-2">
                            @php
                                $intervals = [
                                    ['label' => 'PO → Rilis Dana', 'key' => 'po_to_rilis_diff', 'color' => 'info'],
                                    ['label' => 'Rilis → Invoice', 'key' => 'rilis_to_invoice_diff', 'color' => 'success'],
                                    ['label' => 'Invoice → DO', 'key' => 'invoice_to_do_diff', 'color' => 'warning'],
                                    ['label' => 'DO → Surat Kembali', 'key' => 'do_to_kembali_diff', 'color' => 'danger'],
                                    ['label' => 'Kembali → Lunas', 'key' => 'kembali_to_lunas_diff', 'color' => 'gray'],
                                ];
                            @endphp
                            @foreach($intervals as $int)
                                @php
                                    $diff = $detailData[$int['key']];
                                    $display = $diff !== null ? $diff['text'] : '-';
                                    $totalDays = $diff['totalDays'] ?? 0;
                                    $badgeColor = $diff !== null ? ($totalDays > 30 ? 'danger' : ($totalDays > 14 ? 'warning' : $int['color'])) : 'gray';
                                @endphp
                                <div class="rounded-lg bg-gray-50 dark:bg-white/5 p-2 text-center">
                                    <span class="text-xs text-gray-500 block">{{ $int['label'] }}</span>
                                    <span class="text-sm font-semibold">
                                        <x-filament::badge color="{{ $badgeColor }}" size="sm">{{ $display }}</x-filament::badge>
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- 4. BANK (Company Internal) --}}
                @if($detailData['bank'])
                <div class="rounded-lg border border-gray-200 dark:border-white/10 p-4">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 uppercase tracking-wide">Informasi Bank (Company Internal)</h4>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div>
                            <span class="text-xs text-gray-500 block">Nama Perusahaan</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $detailData['bank']['nama'] }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">Nama Bank</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $detailData['bank']['nama_bank'] }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">No. Rekening</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $detailData['bank']['no_rekening'] }}</span>
                        </div>
                    </div>
                </div>
                @endif

                {{-- 5. DAFTAR BARANG --}}
                @if(count($detailData['items']) > 0)
                <div class="rounded-lg border border-gray-200 dark:border-white/10 p-4">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 uppercase tracking-wide">Daftar Barang</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-white/10">
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama Barang</th>
                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Qty</th>
                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Satuan</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Harga PO</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Supplier</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                                @foreach($detailData['items'] as $item)
                                <tr>
                                    <td class="px-3 py-2 text-gray-900 dark:text-white">{{ $item['name'] }}</td>
                                    <td class="px-3 py-2 text-center text-gray-600">{{ $item['qty'] }}</td>
                                    <td class="px-3 py-2 text-center text-gray-600">{{ $item['satuan'] }}</td>
                                    <td class="px-3 py-2 text-right text-gray-900 dark:text-white">Rp {{ number_format($item['po'], 0, ',', '.') }}</td>
                                    <td class="px-3 py-2 text-gray-600 dark:text-gray-400">{{ $item['supplier'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                {{-- 6. LAPORAN KAS HARIAN --}}
                @if(count($detailData['kas_harian']) > 0)
                <div class="rounded-lg border border-gray-200 dark:border-white/10 p-4">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 uppercase tracking-wide">Kas Harian</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-white/10">
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Tipe</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                                @foreach($detailData['kas_harian'] as $k)
                                <tr>
                                    <td class="px-3 py-2 text-gray-900 dark:text-white">{{ $k['kode'] }}</td>
                                    <td class="px-3 py-2 text-center"><x-filament::badge size="sm">{{ $k['tipe'] }}</x-filament::badge></td>
                                    <td class="px-3 py-2 text-right text-gray-900 dark:text-white">Rp {{ number_format($k['jumlah'], 0, ',', '.') }}</td>
                                    <td class="px-3 py-2 text-gray-600 dark:text-gray-400">{{ $k['keterangan'] }}</td>
                                    <td class="px-3 py-2 text-gray-600 dark:text-gray-400">{{ $k['tanggal'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                {{-- 7. BUKU BESAR --}}
                @if(count($detailData['buku_besar']) > 0)
                <div class="rounded-lg border border-gray-200 dark:border-white/10 p-4">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 uppercase tracking-wide">Buku Besar</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-white/10">
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Debit</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Kredit</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                                @foreach($detailData['buku_besar'] as $b)
                                <tr>
                                    <td class="px-3 py-2 text-gray-900 dark:text-white">{{ $b['kode'] }}</td>
                                    <td class="px-3 py-2 text-gray-600 dark:text-gray-400">{{ $b['deskripsi'] }}</td>
                                    <td class="px-3 py-2 text-right text-gray-900 dark:text-white">Rp {{ number_format($b['debit'] ?? 0, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2 text-right text-gray-900 dark:text-white">Rp {{ number_format($b['kredit'] ?? 0, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2 text-gray-600 dark:text-gray-400">{{ $b['tanggal'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                {{-- 8. PERAN & KETERLIBATAN (SEMUA DIVISI) --}}
                <div class="rounded-lg border border-gray-200 dark:border-white/10 p-4">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 uppercase tracking-wide">Keterlibatan Setiap Divisi</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach(['marketing' => 'Marketing', 'finance' => 'Finance', 'logistik' => 'Logistik'] as $rk => $rl)
                            @php $rd = $detailData['roles'][$rk]; @endphp
                            <div class="rounded-lg border border-gray-200 dark:border-white/10 p-3">
                                <div class="flex items-center justify-between mb-2">
                                    <h5 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $rl }}</h5>
                                    <div class="flex items-center gap-1">
                                        <x-filament::badge color="{{ $rd['batas_waktu_color'] }}" size="sm" class="cursor-pointer" x-on:click="$wire.showBatasWaktuDetail('{{ $rk }}')">{{ $rd['batas_waktu_label'] }}</x-filament::badge>
                                        <x-filament::badge color="{{ $rd['status_color'] }}" size="sm">{{ $rd['status'] }}</x-filament::badge>
                                    </div>
                                </div>

                                @if($rd['batas_waktu_text'])
                                    <p class="text-xs text-gray-500 mb-2">{{ $rd['batas_waktu_text'] }}</p>
                                @endif

                                @if(count($rd['users']) > 0)
                                <div class="mb-2">
                                    <span class="text-xs text-gray-500 block mb-1">PIC:</span>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($rd['users'] as $u)
                                            <x-filament::badge color="primary" size="sm" class="cursor-pointer" x-on:click="open = false; $wire.showUserAudit({{ $u['id'] }})">{{ $u['name'] }}</x-filament::badge>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                @if(count($rd['activities']) > 0)
                                <div>
                                    <span class="text-xs text-gray-500 block mb-1">Riwayat:</span>
                                    <div class="space-y-1.5 max-h-32 overflow-y-auto">
                                        @foreach($rd['activities'] as $a)
                                        <div class="text-xs text-gray-600 dark:text-gray-400 border-l-2 border-gray-300 dark:border-gray-600 pl-2">
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $a['user'] }}</span>
                                            <span class="text-gray-400 ml-1">({{ $a['time'] }})</span>
                                            @if($a['note'])
                                                <p class="mt-0.5 leading-tight">{{ $a['note'] }}</p>
                                            @endif
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                @if(count($rd['users']) === 0 && count($rd['activities']) === 0)
                                    <p class="text-xs text-gray-400">Belum ada aktivitas</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
            @endif

            {{-- Footer --}}
            <div class="flex justify-end px-6 py-4 border-t border-gray-200 dark:border-white/10">
                <x-filament::button color="gray" x-on:click="open = false; $wire.closeDetail()">
                    Tutup
                </x-filament::button>
            </div>
        </div>
    </div>

    {{-- ── MODAL BATAS WAKTU DETAIL (NESTED) ── --}}
    <div
        x-data="{ open: @entangle('showBatasWaktuDetailModal') }"
        x-show="open"
        x-cloak
        x-on:keydown.escape.window="open = false; $wire.closeBatasWaktuDetail()"
        class="fixed inset-0 z-[60] flex items-start justify-center pt-10 pb-10 overflow-y-auto"
    >
        <div x-show="open" x-cloak x-transition.opacity
             class="fixed inset-0 bg-gray-900/60 dark:bg-gray-900/80 backdrop-blur-sm"
             x-on:click="open = false; $wire.closeBatasWaktuDetail()">
        </div>

        <div x-show="open" x-cloak x-transition
             class="relative w-full max-w-3xl bg-white dark:bg-gray-900 rounded-xl shadow-2xl ring-1 ring-gray-950/5 dark:ring-white/10 mx-4">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-white/10">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Detail Batas Waktu: {{ $batasWaktuDetailData['role'] ?? '' }}
                    </h3>
                    <p class="text-sm text-gray-500 mt-0.5">
                        Pesanan: {{ $batasWaktuDetailData['pesanan_code'] ?? '' }}
                    </p>
                </div>
                <button x-on:click="open = false; $wire.closeBatasWaktuDetail()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                    <x-heroicon-m-x-mark class="w-6 h-6" />
                </button>
            </div>

            @if($batasWaktuDetailData)
            <div class="px-6 py-4 space-y-4 max-h-[60vh] overflow-y-auto">

                {{-- Status --}}
                <div class="flex items-center gap-3">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Status:</span>
                    <x-filament::badge color="{{ $batasWaktuDetailData['batas_waktu_color'] }}" size="lg">
                        {{ $batasWaktuDetailData['batas_waktu_label'] }}
                    </x-filament::badge>
                </div>

                @if($batasWaktuDetailData['batas_waktu_text'])
                    <p class="text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-white/5 rounded-lg p-3">
                        {{ $batasWaktuDetailData['batas_waktu_text'] }}
                    </p>
                @endif

                {{-- Timeline --}}
                <div class="rounded-lg border border-gray-200 dark:border-white/10 p-4">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 uppercase tracking-wide">Timeline Tugas</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <span class="text-xs text-gray-500 block">ID Tugas</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $batasWaktuDetailData['task_id'] ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">Tenggat</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $batasWaktuDetailData['task_due_date'] ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">Dibuat</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $batasWaktuDetailData['task_created_at'] }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">Diperbarui</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $batasWaktuDetailData['task_updated_at'] }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">Jam sejak dibuat</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $batasWaktuDetailData['task_hours_since_creation'] }} jam</span>
                        </div>
                        @if($batasWaktuDetailData['task_hours_to_complete'] !== null)
                        <div>
                            <span class="text-xs text-gray-500 block">Durasi penyelesaian</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $batasWaktuDetailData['task_hours_to_complete'] }} jam</span>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Riwayat Aktivitas --}}
                @if(count($batasWaktuDetailData['activities']) > 0)
                <div class="rounded-lg border border-gray-200 dark:border-white/10 p-4">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 uppercase tracking-wide">Riwayat Aktivitas</h4>
                    <div class="space-y-2 max-h-48 overflow-y-auto">
                        @foreach($batasWaktuDetailData['activities'] as $a)
                        <div class="text-xs text-gray-600 dark:text-gray-400 border-l-2 border-gray-300 dark:border-gray-600 pl-2 py-1">
                            <span class="font-medium text-gray-900 dark:text-white">{{ $a['user'] }}</span>
                            <span class="text-gray-400 ml-1">({{ $a['time'] }})</span>
                            @if($a['note'])
                                <p class="mt-0.5 leading-tight">{{ $a['note'] }}</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>
            @endif

            <div class="flex justify-end px-6 py-4 border-t border-gray-200 dark:border-white/10">
                <x-filament::button color="gray" x-on:click="open = false; $wire.closeBatasWaktuDetail()">
                    Tutup
                </x-filament::button>
            </div>
        </div>
    </div>

    {{-- ── MODAL DETAIL DIVISI ── --}}
    <div
        x-data="{ open: @entangle('showDivisiDetailModal') }"
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 flex items-start justify-center pt-10 pb-10 overflow-y-auto"
        x-on:keydown.escape.window="open = false; $wire.closeDivisiDetail()"
    >
        {{-- Overlay --}}
        <div x-show="open" x-cloak x-transition.opacity
             class="fixed inset-0 bg-gray-900/50 dark:bg-gray-900/80 backdrop-blur-sm"
             x-on:click="open = false; $wire.closeDivisiDetail()">
        </div>

        {{-- Modal Content --}}
        <div x-show="open" x-cloak x-transition
             class="relative w-full max-w-6xl bg-white dark:bg-gray-900 rounded-xl shadow-2xl ring-1 ring-gray-950/5 dark:ring-white/10 mx-4">
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-white/10">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Detail Divisi: {{ $divisiDetailData['role_label'] ?? '...' }}
                    </h3>
                    <p class="text-sm text-gray-500 mt-0.5">
                        Informasi user, tugas, dan aktivitas pada divisi ini
                    </p>
                </div>
                <button x-on:click="open = false; $wire.closeDivisiDetail()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                    <x-heroicon-m-x-mark class="w-6 h-6" />
                </button>
            </div>

            {{-- Body --}}
            @if($divisiDetailData)
            <div class="px-6 py-4 space-y-6 max-h-[75vh] overflow-y-auto">

                {{-- 1. STATISTIK DIVISI --}}
                <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                    <div class="rounded-lg border border-gray-200 dark:border-white/10 p-3 text-center">
                        <span class="text-2xl font-bold text-primary-600">{{ $divisiDetailData['total_tasks'] }}</span>
                        <p class="text-xs text-gray-500 mt-1">Total Tugas</p>
                    </div>
                    <div class="rounded-lg border border-gray-200 dark:border-white/10 p-3 text-center">
                        <span class="text-2xl font-bold text-success-600">{{ $divisiDetailData['completed_tasks'] }}</span>
                        <p class="text-xs text-gray-500 mt-1">Selesai</p>
                    </div>
                    <div class="rounded-lg border border-gray-200 dark:border-white/10 p-3 text-center">
                        <span class="text-2xl font-bold text-warning-600">{{ $divisiDetailData['in_progress_tasks'] }}</span>
                        <p class="text-xs text-gray-500 mt-1">Proses</p>
                    </div>
                    <div class="rounded-lg border border-gray-200 dark:border-white/10 p-3 text-center">
                        <span class="text-2xl font-bold text-gray-600">{{ $divisiDetailData['pending_tasks'] }}</span>
                        <p class="text-xs text-gray-500 mt-1">Pending</p>
                    </div>
                    <div class="rounded-lg border border-gray-200 dark:border-white/10 p-3 text-center">
                        @php $pct = $divisiDetailData['completion_percentage']; @endphp
                        <span class="text-2xl font-bold {{ $pct >= 100 ? 'text-success-600' : ($pct > 0 ? 'text-warning-600' : 'text-gray-600') }}">{{ $pct }}%</span>
                        <p class="text-xs text-gray-500 mt-1">Kompleksi</p>
                    </div>
                </div>

                {{-- 2. USER & AKTIVITAS --}}
                <div class="rounded-lg border border-gray-200 dark:border-white/10 p-4"
                     x-data="{ userPage: 1, perPage: 5, totalUsers: {{ count($divisiDetailData['user_activities']) }} }">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 uppercase tracking-wide">
                        Anggota Divisi & Aktivitas
                    </h4>

                    @forelse($divisiDetailData['user_activities'] as $ui => $ua)
                        <div x-show="Math.ceil(({{ $ui }} + 1) / perPage) === userPage" style="display: none"
                             class="mb-4 last:mb-0 rounded-lg border border-gray-100 dark:border-white/5 p-3"
                             x-data="{ actPage: 1, actPerPage: 5, totalActs: {{ count($ua['activities']) }} }">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <x-filament::badge color="primary" size="sm">
                                        {{ $ua['user_name'] }}
                                    </x-filament::badge>
                                    <span class="text-xs text-gray-500">
                                        {{ $ua['total_activities'] }} aktivitas
                                    </span>
                                </div>
                                <x-filament::button color="info" size="xs" x-on:click="open = false; $wire.showUserAudit({{ $ua['user_id'] }})">
                                    Audit
                                </x-filament::button>
                            </div>

                            @if(count($ua['activities']) > 0)
                                <div class="space-y-1.5">
                                    @foreach($ua['activities'] as $ai => $act)
                                        <div x-show="Math.ceil(({{ $ai }} + 1) / actPerPage) === actPage" style="display: none"
                                             class="text-xs text-gray-600 dark:text-gray-400 border-l-2 {{ $act['as_creator'] ? 'border-primary-400' : 'border-gray-400' }} pl-2 py-0.5">
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $act['pesanan_code'] }}</span>
                                            <span class="text-gray-400 ml-1">({{ $act['time'] }})</span>
                                            @if($act['note'] !== '-')
                                                <p class="mt-0.5 leading-tight">{{ $act['note'] }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                @if(count($ua['activities']) > 5)
                                    <div class="flex items-center justify-between pt-2 mt-2 border-t border-gray-100 dark:border-white/5">
                                        <button type="button" @click.prevent="actPage = Math.max(1, actPage - 1)" :disabled="actPage === 1"
                                                class="text-xs font-medium text-primary-600 hover:text-primary-500 disabled:text-gray-400 disabled:cursor-not-allowed transition">
                                            &laquo;
                                        </button>
                                        <span class="text-xs text-gray-500" x-text="`${actPage} / ${Math.ceil(totalActs / actPerPage)}`"></span>
                                        <button type="button" @click.prevent="actPage = Math.min(Math.ceil(totalActs / actPerPage), actPage + 1)" :disabled="actPage === Math.ceil(totalActs / actPerPage)"
                                                class="text-xs font-medium text-primary-600 hover:text-primary-500 disabled:text-gray-400 disabled:cursor-not-allowed transition">
                                            &raquo;
                                        </button>
                                    </div>
                                @endif
                            @else
                                <p class="text-xs text-gray-400">Belum ada aktivitas</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">Tidak ada anggota divisi ini</p>
                    @endforelse

                    @if(count($divisiDetailData['user_activities']) > 5)
                        <div class="flex items-center justify-between pt-3 mt-3 border-t border-gray-200 dark:border-white/10">
                            <button type="button" @click.prevent="userPage = Math.max(1, userPage - 1)" :disabled="userPage === 1"
                                    class="text-xs font-medium text-primary-600 hover:text-primary-500 disabled:text-gray-400 disabled:cursor-not-allowed transition">
                                &laquo; Sebelumnya
                            </button>
                            <span class="text-xs text-gray-500" x-text="`${userPage} / ${Math.ceil(totalUsers / perPage)}`"></span>
                            <button type="button" @click.prevent="userPage = Math.min(Math.ceil(totalUsers / perPage), userPage + 1)" :disabled="userPage === Math.ceil(totalUsers / perPage)"
                                    class="text-xs font-medium text-primary-600 hover:text-primary-500 disabled:text-gray-400 disabled:cursor-not-allowed transition">
                                Selanjutnya &raquo;
                            </button>
                        </div>
                    @endif
                </div>

                {{-- 3. STATISTIK PER USER --}}
                @if(count($divisiDetailData['user_stats']) > 0)
                <div class="rounded-lg border border-gray-200 dark:border-white/10 p-4">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 uppercase tracking-wide">
                        Progress Per User
                    </h4>
                    <div class="space-y-3">
                        @foreach($divisiDetailData['user_stats'] as $us)
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $us['name'] }}</span>
                                    <span class="text-gray-500">{{ $us['completed'] }}/{{ $us['total'] }} ({{ $us['percentage'] }}%)</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                                    <div class="bg-{{ $us['percentage'] >= 100 ? 'success' : ($us['percentage'] > 0 ? 'primary' : 'gray') }}-500 h-2 rounded-full transition-all"
                                         style="width: {{ $us['percentage'] }}%">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- 4. DAFTAR TUGAS TERBARU --}}
                <div class="rounded-lg border border-gray-200 dark:border-white/10 p-4">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 uppercase tracking-wide">
                        Tugas Terbaru
                    </h4>
                    <div x-data="{ page: 1, perPage: 5, total: {{ count($divisiDetailData['tasks']) }} }">
                        <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-white/10">
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">No. Pesanan</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Judul Tugas</th>
                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Tenggat</th>
                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Dibuat</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                                @forelse($divisiDetailData['tasks'] as $i => $task)
                                    <tr x-show="Math.ceil(({{ $i }} + 1) / perPage) === page"
                                        style="display: none"
                                        class="hover:bg-gray-50 dark:hover:bg-white/5">
                                        <td class="px-3 py-2 font-medium text-gray-900 dark:text-white">{{ $task['pesanan_code'] }}</td>
                                        <td class="px-3 py-2 text-gray-600 dark:text-gray-400 max-w-xs truncate">{{ $task['title'] }}</td>
                                        <td class="px-3 py-2 text-center">
                                            @php
                                                $sc = $task['status'] === 2 ? 'success' : ($task['status'] === 1 ? 'warning' : 'gray');
                                            @endphp
                                            <x-filament::badge color="{{ $sc }}" size="sm">{{ $task['status_label'] }}</x-filament::badge>
                                        </td>
                                        <td class="px-3 py-2 text-center text-gray-600 dark:text-gray-400">{{ $task['due_date'] ?? '-' }}</td>
                                        <td class="px-3 py-2 text-center text-gray-600 dark:text-gray-400">{{ $task['created_at'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-3 py-4 text-center text-sm text-gray-500">Belum ada tugas</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        </div>
                        @if(count($divisiDetailData['tasks']) > 5)
                            <div class="flex items-center justify-between pt-3 border-t border-gray-200 dark:border-white/10 mt-3">
                                <button type="button" @click.prevent="page = Math.max(1, page - 1)" :disabled="page === 1"
                                        class="text-xs font-medium text-primary-600 hover:text-primary-500 disabled:text-gray-400 disabled:cursor-not-allowed transition">
                                    &laquo; Sebelumnya
                                </button>
                                <span class="text-xs text-gray-500" x-text="`${page} / ${Math.ceil(total / perPage)}`"></span>
                                <button type="button" @click.prevent="page = Math.min(Math.ceil(total / perPage), page + 1)" :disabled="page === Math.ceil(total / perPage)"
                                        class="text-xs font-medium text-primary-600 hover:text-primary-500 disabled:text-gray-400 disabled:cursor-not-allowed transition">
                                    Selanjutnya &raquo;
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
            @endif

            {{-- Footer --}}
            <div class="flex justify-end px-6 py-4 border-t border-gray-200 dark:border-white/10">
                <x-filament::button color="gray" x-on:click="open = false; $wire.closeDivisiDetail()">
                    Tutup
                </x-filament::button>
            </div>
        </div>
    </div>

    {{-- ── MODAL DETAIL CHART ── --}}
    <div
        x-data="{ open: @entangle('showChartDetailModal') }"
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 flex items-start justify-center pt-10 pb-10 overflow-y-auto"
        x-on:keydown.escape.window="open = false; $wire.closeChartDetail()"
    >
        {{-- Overlay --}}
        <div x-show="open" x-cloak x-transition.opacity
             class="fixed inset-0 bg-gray-900/50 dark:bg-gray-900/80 backdrop-blur-sm"
             x-on:click="open = false; $wire.closeChartDetail()">
        </div>

        {{-- Modal Content --}}
        <div x-show="open" x-cloak x-transition
             class="relative w-full max-w-5xl bg-white dark:bg-gray-900 rounded-xl shadow-2xl ring-1 ring-gray-950/5 dark:ring-white/10 mx-4">
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-white/10">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $chartDetailData['title'] ?? 'Detail Chart' }}
                    </h3>
                    <p class="text-sm text-gray-500 mt-0.5">
                        Menampilkan {{ $chartDetailData['total'] ?? 0 }} data
                    </p>
                </div>
                <button x-on:click="open = false; $wire.closeChartDetail()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                    <x-heroicon-m-x-mark class="w-6 h-6" />
                </button>
            </div>

            {{-- Body --}}
            @if($chartDetailData)
            <div class="px-6 py-4 max-h-[75vh] overflow-y-auto">
                <div class="rounded-lg border border-gray-200 dark:border-white/10 p-4">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 uppercase tracking-wide">
                        Daftar Pesanan
                    </h4>
                    <div x-data="{ page: 1, perPage: 5, total: {{ count($chartDetailData['orders']) }} }">
                        <div class="overflow-x-auto max-h-96 overflow-y-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-white/10">
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">No. Pesanan</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Perusahaan</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Group</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dibuat Oleh</th>
                                    @if($chartDetailData['chart'] === 'marketing-performa')
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total Harga</th>
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Modal</th>
                                    @endif
                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                                @php $colspan = $chartDetailData['chart'] === 'marketing-performa' ? 8 : 6; @endphp
                                @forelse($chartDetailData['orders'] as $i => $order)
                                    <tr x-show="Math.ceil(({{ $i }} + 1) / perPage) === page"
                                        style="display: none" class="hover:bg-gray-50 dark:hover:bg-white/5">
                                        <td class="px-3 py-2 font-medium text-gray-900 dark:text-white">{{ $order['code'] }}</td>
                                        <td class="px-3 py-2 text-gray-600 dark:text-gray-400">{{ $order['company_name'] }}</td>
                                        <td class="px-3 py-2 text-gray-600 dark:text-gray-400">{{ $order['group_name'] }}</td>
                                        <td class="px-3 py-2 text-gray-600 dark:text-gray-400">{{ $order['created_by'] }}</td>
                                        @if($chartDetailData['chart'] === 'marketing-performa')
                                            <td class="px-3 py-2 text-right text-gray-900 dark:text-white font-medium whitespace-nowrap">{{ $order['total_formatted'] ?? '-' }}</td>
                                            <td class="px-3 py-2 text-right text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $order['modal_formatted'] ?? '-' }}</td>
                                        @endif
                                        <td class="px-3 py-2 text-center">
                                            @php
                                                $sc = match($order['status_label']) {
                                                    'Selesai', 'Ditandai Lunas' => 'success',
                                                    'Pending', 'Perlu Rilis Dana' => 'warning',
                                                    default => 'gray',
                                                };
                                            @endphp
                                            <x-filament::badge color="{{ $sc }}" size="sm">{{ $order['status_label'] }}</x-filament::badge>
                                        </td>
                                        <td class="px-3 py-2 text-center text-gray-600 dark:text-gray-400">{{ $order['created_at'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $colspan }}" class="px-3 py-4 text-center text-sm text-gray-500">Tidak ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        </div>
                        @if(count($chartDetailData['orders']) > 5)
                            <div class="flex items-center justify-between pt-3 mt-3 border-t border-gray-200 dark:border-white/10">
                                <button type="button" @click.prevent="page = Math.max(1, page - 1)" :disabled="page === 1"
                                        class="text-xs font-medium text-primary-600 hover:text-primary-500 disabled:text-gray-400 disabled:cursor-not-allowed transition">
                                    &laquo; Sebelumnya
                                </button>
                                <span class="text-xs text-gray-500" x-text="`${page} / ${Math.ceil(total / perPage)}`"></span>
                                <button type="button" @click.prevent="page = Math.min(Math.ceil(total / perPage), page + 1)" :disabled="page === Math.ceil(total / perPage)"
                                        class="text-xs font-medium text-primary-600 hover:text-primary-500 disabled:text-gray-400 disabled:cursor-not-allowed transition">
                                    Selanjutnya &raquo;
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- Footer --}}
            <div class="flex justify-end px-6 py-4 border-t border-gray-200 dark:border-white/10">
                <x-filament::button color="gray" x-on:click="open = false; $wire.closeChartDetail()">
                    Tutup
                </x-filament::button>
            </div>
        </div>
    </div>

    {{-- ── MODAL MARKETING OVERALL ── --}}
    <div
        x-data="{ open: @entangle('showMarketingOverallModal') }"
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 flex items-start justify-center pt-10 pb-10 overflow-y-auto"
        x-on:keydown.escape.window="open = false; $wire.closeMarketingOverall()"
    >
        <div x-show="open" x-cloak x-transition.opacity
             class="fixed inset-0 bg-gray-900/50 dark:bg-gray-900/80 backdrop-blur-sm"
             x-on:click="open = false; $wire.closeMarketingOverall()">
        </div>

        <div x-show="open" x-cloak x-transition
             class="relative w-full max-w-4xl bg-white dark:bg-gray-900 rounded-xl shadow-2xl ring-1 ring-gray-950/5 dark:ring-white/10 mx-4">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-white/10">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Data Keseluruhan Marketing
                    </h3>
                    <p class="text-sm text-gray-500 mt-0.5">
                        Jumlah pesanan per marketing per bulan
                    </p>
                </div>
                <button x-on:click="open = false; $wire.closeMarketingOverall()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                    <x-heroicon-m-x-mark class="w-6 h-6" />
                </button>
            </div>

            @if($marketingOverallData)
            <div class="px-6 py-4 max-h-[70vh] overflow-y-auto">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-white/10">
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Marketing</th>
                                @foreach($marketingOverallData['months'] as $month)
                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">{{ $month }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                            @foreach($marketingOverallData['users'] as $user)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition">
                                    <td class="px-3 py-2 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                        {{ $user['name'] }}
                                    </td>
                                    @foreach($marketingOverallData['months'] as $month)
                                        <td class="px-3 py-2 text-center text-gray-600 dark:text-gray-400">
                                            {{ $user['data'][$month] ?? 0 }}
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 border-gray-300 dark:border-white/20 bg-gray-50 dark:bg-white/5">
                                <td class="px-3 py-2 font-semibold text-gray-900 dark:text-white">Total</td>
                                @foreach($marketingOverallData['months'] as $month)
                                    <td class="px-3 py-2 text-center font-semibold text-gray-900 dark:text-white">
                                        {{ $marketingOverallData['totals'][$month] ?? 0 }}
                                    </td>
                                @endforeach
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @if($marketingOverallLastPage > 1)
                    <div class="flex items-center justify-between pt-4 mt-4 border-t border-gray-200 dark:border-white/10">
                        <x-filament::button
                            color="gray" size="xs"
                            wire:click="setMarketingOverallPage({{ $marketingOverallPage - 1 }})"
                            :disabled="$marketingOverallPage <= 1"
                        >
                            &laquo; Sebelumnya
                        </x-filament::button>
                        <span class="text-xs text-gray-500">
                            Halaman {{ $marketingOverallPage }} dari {{ $marketingOverallLastPage }}
                        </span>
                        <x-filament::button
                            color="gray" size="xs"
                            wire:click="setMarketingOverallPage({{ $marketingOverallPage + 1 }})"
                            :disabled="$marketingOverallPage >= $marketingOverallLastPage"
                        >
                            Selanjutnya &raquo;
                        </x-filament::button>
                    </div>
                @endif
            </div>
            @endif

            <div class="flex justify-end px-6 py-4 border-t border-gray-200 dark:border-white/10">
                <x-filament::button color="gray" x-on:click="open = false; $wire.closeMarketingOverall()">
                    Tutup
                </x-filament::button>
            </div>
        </div>
    </div>

    {{-- ── MODAL AUDIT USER ── --}}
    <div
        x-data="{ open: @entangle('showUserAuditModal') }"
        x-show="open"
        x-cloak
        x-on:keydown.escape.window="open = false; $wire.closeUserAudit()"
        class="fixed inset-0 z-50 flex items-start justify-center pt-10 pb-10 overflow-y-auto"
        x-init="
            $watch('open', value => {
                if (value) {
                    if (window._userAuditBarChart) { window._userAuditBarChart.destroy(); window._userAuditBarChart = null; }
                    if (window._userAuditPieChart) { window._userAuditPieChart.destroy(); window._userAuditPieChart = null; }
                    setTimeout(async () => {
                        if (!window.Chart) return;
                        const data = await $wire.get('userAuditData');
                        if (!data || !data.stats) return;
                        const labels = Object.keys(data.stats).filter(k => data.stats[k] > 0 || k === 'Tepat Waktu');
                        const vals = labels.map(k => data.stats[k]);
                        const colors = { 'Tepat Waktu': '#10b981', 'Terlambat': '#ef4444', 'Dalam Proses': '#3b82f6' };
                        const bgColors = labels.map(k => colors[k] || '#6b7280');
                        const barCanvas = document.getElementById('userAuditBarChart');
                        if (barCanvas) {
                            window._userAuditBarChart = new Chart(barCanvas.getContext('2d'), {
                                type: 'bar',
                                data: {
                                    labels: labels,
                                    datasets: [{
                                        label: 'Jumlah Tugas',
                                        data: vals,
                                        backgroundColor: bgColors,
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: { display: false },
                                        tooltip: { callbacks: { label: ctx => ctx.parsed.y + ' tugas' } }
                                    },
                                    scales: {
                                        y: { beginAtZero: true, ticks: { stepSize: 1 } }
                                    }
                                }
                            });
                        }
                        const pieCanvas = document.getElementById('userAuditPieChart');
                        if (pieCanvas) {
                            window._userAuditPieChart = new Chart(pieCanvas.getContext('2d'), {
                                type: 'pie',
                                data: {
                                    labels: labels,
                                    datasets: [{
                                        data: vals,
                                        backgroundColor: bgColors,
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: { position: 'right', labels: { boxWidth: 12, padding: 8, font: { size: 11 } } }
                                    }
                                }
                            });
                        }
                    }, 200);
                }
            });
        "
    >
        <div x-show="open" x-cloak x-transition.opacity
             class="fixed inset-0 bg-gray-900/50 dark:bg-gray-900/80 backdrop-blur-sm"
             x-on:click="open = false; $wire.closeUserAudit()">
        </div>

        <div x-show="open" x-cloak x-transition
             class="relative w-full max-w-6xl bg-white dark:bg-gray-900 rounded-xl shadow-2xl ring-1 ring-gray-950/5 dark:ring-white/10 mx-4">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-white/10">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Audit User: {{ $userAuditData['user']['name'] ?? '...' }}
                    </h3>
                    <p class="text-sm text-gray-500 mt-0.5">
                        {{ $userAuditData['user']['role'] ?? '' }} &mdash; {{ $userAuditData['user']['email'] ?? '' }}
                    </p>
                </div>
                <button x-on:click="open = false; $wire.closeUserAudit()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                    <x-heroicon-m-x-mark class="w-6 h-6" />
                </button>
            </div>

            @if($userAuditData)
            <div class="px-6 py-4 space-y-6 max-h-[75vh] overflow-y-auto">

                {{-- STATS CARDS --}}
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                    @foreach($userAuditData['stats'] as $label => $count)
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

                {{-- CHARTS --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="rounded-lg border border-gray-200 dark:border-white/10 p-4">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 uppercase tracking-wide">Grafik Batang</h4>
                        <div class="max-h-44"><canvas id="userAuditBarChart"></canvas></div>
                    </div>
                    <div class="rounded-lg border border-gray-200 dark:border-white/10 p-4">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 uppercase tracking-wide">Grafik Lingkaran</h4>
                        <div class="max-h-44"><canvas id="userAuditPieChart"></canvas></div>
                    </div>
                </div>

                {{-- TASK LIST --}}
                <div class="rounded-lg border border-gray-200 dark:border-white/10 p-4">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 uppercase tracking-wide">
                        Daftar Tugas ({{ count($userAuditData['tasks']) }})
                    </h4>
                    <div x-data="{ page: 1, perPage: 5, total: {{ count($userAuditData['tasks']) }} }">
                        <div class="overflow-x-auto">
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
                                    @forelse($userAuditData['tasks'] as $i => $t)
                                        <tr x-show="Math.ceil(({{ $i }} + 1) / perPage) === page"
                                            style="display: none" class="hover:bg-gray-50 dark:hover:bg-white/5">
                                            <td class="px-3 py-2 font-medium text-gray-900 dark:text-white">{{ $t['pesanan_code'] }}</td>
                                            <td class="px-3 py-2 text-center text-gray-600 dark:text-gray-400">{{ ucfirst($t['role']) }}</td>
                                            <td class="px-3 py-2 text-center">
                                                @php
                                                    $sc = match($t['status']) { 2 => 'success', 1 => 'warning', default => 'gray' };
                                                    $sl = match($t['status']) { 2 => 'Selesai', 1 => 'Proses', default => 'Pending' };
                                                @endphp
                                                <x-filament::badge color="{{ $sc }}" size="sm">{{ $sl }}</x-filament::badge>
                                            </td>
                                            <td class="px-3 py-2 text-center">
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
                                            <td class="px-3 py-2 text-right text-gray-600 dark:text-gray-400">{{ $t['duration'] }}</td>
                                            <td class="px-3 py-2 text-right text-gray-600 dark:text-gray-400">{{ $t['created_at'] }}</td>
                                            <td class="px-3 py-2 text-right text-gray-600 dark:text-gray-400">{{ $t['completed_at'] }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-3 py-4 text-center text-sm text-gray-500">Tidak ada tugas</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if(count($userAuditData['tasks']) > 5)
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

            </div>
            @endif

            <div class="flex justify-end px-6 py-4 border-t border-gray-200 dark:border-white/10">
                <x-filament::button color="gray" x-on:click="open = false; $wire.closeUserAudit()">
                    Tutup
                </x-filament::button>
            </div>
        </div>
    </div>

    <x-filament-actions::modals />

    <script>
        window.filamentChartJsPlugins = [{
            id: 'datalabels',
            afterDraw: function(chart) {
                try {
                    var cfg = chart.options && chart.options.plugins && chart.options.plugins.datalabels;
                    if (!cfg || !cfg.display) return;
                    var meta = chart.getDatasetMeta(0);
                    if (!meta || !meta.data || !meta.data.length) return;
                    var isArc = false;
                    for (var si = 0; si < meta.data.length; si++) {
                        if (meta.data[si] && meta.data[si].startAngle !== void 0) { isArc = true; break; }
                    }
                    if (!isArc) return;
                    var ctx = chart.ctx;
                    if (!ctx) return;
                    ctx.save();
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.font = 'bold 12px Arial';
                    ctx.fillStyle = '#ffffff';
                    for (var i = 0; i < meta.data.length; i++) {
                        var el = meta.data[i];
                        var lab = chart.data.labels[i];
                        if (!el || !lab || el.startAngle === void 0 || el.endAngle === void 0) continue;
                        if (el.x === void 0 || el.y === void 0 || isNaN(el.startAngle)) continue;
                        if (el.hidden) continue;
                        if (Math.abs(el.endAngle - el.startAngle) < 0.05) continue;
                        ctx.fillText(lab, el.x + Math.cos((el.startAngle + el.endAngle) / 2) * ((el.outerRadius + (el.innerRadius || 0)) / 2 * 0.65), el.y + Math.sin((el.startAngle + el.endAngle) / 2) * ((el.outerRadius + (el.innerRadius || 0)) / 2 * 0.65));
                    }
                    ctx.restore();
                } catch(e) {}
            }
        }];
    </script>

    @script
    <script>
        document.addEventListener('click', function(e) {
            if (e.target.id === 'marketing-overall-btn' || e.target.closest('#marketing-overall-btn')) {
                $wire.showMarketingOverall();
            }
        });

        window.addEventListener('chart-clicked', function(e) {
            var d = e.detail;
            $wire.showChartDetail(
                d.chart,
                d.label,
                d.value,
                d.datasetLabel || '',
                d.index || 0
            );
        });
    </script>
    @endscript
</x-filament-panels::page>
