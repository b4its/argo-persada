<x-filament-panels::page>

    <div class="space-y-6">

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
                </div>
            @endforeach
        </div>

        {{-- ── RECENT ACTIVITIES ── --}}
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-white/10">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                    Aktifitas Terbaru
                </h3>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-white/5 max-h-80 overflow-y-auto">
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
                                    <x-filament::badge color="{{ $rd['status_color'] }}" size="sm">{{ $rd['status'] }}</x-filament::badge>
                                </div>

                                @if(count($rd['users']) > 0)
                                <div class="mb-2">
                                    <span class="text-xs text-gray-500 block mb-1">PIC:</span>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($rd['users'] as $u)
                                            <x-filament::badge color="primary" size="sm">{{ $u }}</x-filament::badge>
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

    <x-filament-actions::modals />
</x-filament-panels::page>
