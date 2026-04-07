<x-filament-panels::page>

<div class="flex flex-col gap-6">

        {{-- ╔══════════════════════════════╗
             ║   STAT CARDS (Modern Style)  ║
             ╚══════════════════════════════╝ --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            
            {{-- Total Pesanan --}}
            <div class="relative flex flex-col rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="flex items-start justify-between">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                        <x-filament::icon icon="heroicon-o-shopping-bag" class="h-6 w-6" />
                    </div>
                    {{-- Dummy Trend Badge (Sesuai referensi gambar) --}}
                    <span class="inline-flex items-center gap-x-1 rounded-md bg-emerald-50 px-2 py-1 text-xs font-bold text-emerald-600 ring-1 ring-inset ring-emerald-500/10 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20">
                        <x-filament::icon icon="heroicon-m-arrow-trending-up" class="h-3 w-3" />
                        12.5%
                    </span>
                </div>
                <div class="mt-4">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Pesanan</h3>
                    <p class="mt-1 text-3xl font-bold tracking-tight text-gray-950 dark:text-white">{{ $this->stats['total_pesanan'] }}</p>
                </div>
                {{-- Decorative Progress Bar --}}
                <div class="mt-5 w-full h-1.5 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                    <div class="h-full rounded-full bg-indigo-500" style="width: 65%"></div>
                </div>
            </div>

            {{-- Task Aktif --}}
            <div class="relative flex flex-col rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="flex items-start justify-between">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                        <x-filament::icon icon="heroicon-o-clock" class="h-6 w-6" />
                    </div>
                    {{-- Dummy Trend Badge --}}
                    <span class="inline-flex items-center gap-x-1 rounded-md bg-rose-50 px-2 py-1 text-xs font-bold text-rose-600 ring-1 ring-inset ring-rose-500/10 dark:bg-rose-500/10 dark:text-rose-400 dark:ring-rose-500/20">
                        <x-filament::icon icon="heroicon-m-arrow-trending-down" class="h-3 w-3" />
                        5.2%
                    </span>
                </div>
                <div class="mt-4">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Task Aktif</h3>
                    <p class="mt-1 text-3xl font-bold tracking-tight text-gray-950 dark:text-white">{{ $this->stats['active_tasks'] }}</p>
                </div>
                {{-- Decorative Progress Bar --}}
                <div class="mt-5 w-full h-1.5 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                    <div class="h-full rounded-full bg-amber-500" style="width: 45%"></div>
                </div>
            </div>

            {{-- Task Selesai --}}
            <div class="relative flex flex-col rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="flex items-start justify-between">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                        <x-filament::icon icon="heroicon-o-check-circle" class="h-6 w-6" />
                    </div>
                    {{-- Dummy Trend Badge --}}
                    <span class="inline-flex items-center gap-x-1 rounded-md bg-emerald-50 px-2 py-1 text-xs font-bold text-emerald-600 ring-1 ring-inset ring-emerald-500/10 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20">
                        <x-filament::icon icon="heroicon-m-arrow-trending-up" class="h-3 w-3" />
                        8.4%
                    </span>
                </div>
                <div class="mt-4">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Task Selesai</h3>
                    <p class="mt-1 text-3xl font-bold tracking-tight text-gray-950 dark:text-white">{{ $this->stats['completed_tasks'] }}</p>
                </div>
                {{-- Decorative Progress Bar --}}
                <div class="mt-5 w-full h-1.5 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                    <div class="h-full rounded-full bg-emerald-500" style="width: 80%"></div>
                </div>
            </div>

        </div>

        {{-- ╔══════════════════════════════════════════════════╗
             ║   MAIN CONTENT                                   ║
             ╚══════════════════════════════════════════════════╝ --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">

            {{-- ── LEADERBOARD (Native Widget Style) ── --}}
            <div class="col-span-1">
                <div class="flex flex-col rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 h-full">
                    
                    <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-200 dark:border-white/10">
                        <x-filament::icon icon="heroicon-o-trophy" class="h-6 w-6 text-amber-500" />
                        <div>
                            <h3 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">Leaderboard</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Top 5 performa tim</p>
                        </div>
                    </div>

                    <div class="divide-y divide-gray-200 dark:divide-white/10">
                        @foreach($this->performers as $index => $user)
                            @php $rank = $index + 1; @endphp
                            <div class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50 dark:hover:bg-white/5 transition-colors" style="padding:0.5em">
                                
                                <div class="w-8 h-8 flex flex-shrink-0 items-center justify-center rounded-full text-sm font-bold shadow-sm ring-1 ring-gray-950/10 dark:ring-white/20
                                    @if($rank === 1) bg-amber-400 text-white ring-amber-500
                                    @elseif($rank === 2) bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200
                                    @elseif($rank === 3) bg-orange-400 text-white ring-orange-500
                                    @else bg-gray-50 text-gray-500 dark:bg-white/5 dark:text-gray-400
                                    @endif">
                                    @if($rank === 1)🥇@elseif($rank === 2)🥈@elseif($rank === 3)🥉@else{{ $rank }}@endif
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-950 dark:text-white truncate">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $user->email }}</p>
                                </div>

                                <div class="text-right">
                                    <p class="text-lg font-bold text-indigo-600 dark:text-indigo-400 leading-none">{{ $user->completed_tasks }}</p>
                                    <p class="text-[10px] font-medium text-gray-500 uppercase tracking-widest mt-1">Done</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ── ROLE PROGRESS (Native Table Style) ── --}}
            <div class="col-span-1 lg:col-span-3 space-y-6">
                
                @foreach($this->roleProgressData as $data)
                    {{-- Container Tabel Utama Filament --}}
                    <div x-data="{ search: '' }" class="fi-ta-ctn flex flex-col rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                        
                        {{-- Header Tabel (Sama seperti halaman Karyawan) --}}
                        <div class="fi-ta-header flex flex-col gap-3 p-4 sm:px-6 sm:py-5 sm:flex-row sm:items-center sm:justify-between">
                            
                            <div class="flex items-center gap-4">
                                <h3 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                    {{ strtoupper($data['role']) }}
                                </h3>
                                <x-filament::badge color="success" size="sm">
                                    {{ $data['percentage'] }}% Selesai
                                </x-filament::badge>
                                <x-filament::badge color="gray" size="sm">
                                    {{ $data['completed'] }} / {{ $data['total'] }} Task
                                </x-filament::badge>
                            </div>

                            <div class="w-full sm:w-72">
                                <x-filament::input.wrapper icon="heroicon-m-magnifying-glass">
                                    <x-filament::input
                                        type="text"
                                        x-model="search"
                                        placeholder="Search No. PO..."
                                    />
                                </x-filament::input.wrapper>
                            </div>

                        </div>

                        {{-- Isi Tabel --}}
                        <div class="fi-ta-content relative divide-y divide-gray-200 overflow-x-auto dark:divide-white/10 border-t border-gray-200 dark:border-white/10">
                            <table class="fi-ta-table w-full table-auto text-left text-sm">
                                <thead class="bg-gray-50 dark:bg-white/5">
                                    <tr>
                                        <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 font-semibold text-gray-950 dark:text-white">Code</th>
                                        <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 font-semibold text-gray-950 dark:text-white">No. PO</th>
                                        <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 font-semibold text-gray-950 dark:text-white">Perusahaan</th>
                                        <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 font-semibold text-gray-950 dark:text-white text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                                    @forelse($data['pesanan'] as $p)
                                        <tr 
                                            x-show="search === '' || '{{ addslashes($p->no_po ?? '') }}'.toLowerCase().includes(search.toLowerCase())" 
                                            class="fi-ta-row hover:bg-gray-50 dark:hover:bg-white/5 transition duration-75 " 
                                        >
                                            <td class="fi-ta-cell px-3 py-4 sm:first-of-type:ps-6 sm:last-of-type:pe-6 text-gray-500 dark:text-gray-400"  style="padding:1em">
                                                {{ $p->code }}
                                            </td>
                                            <td class="fi-ta-cell px-3 py-4 sm:first-of-type:ps-6 sm:last-of-type:pe-6 font-medium text-gray-950 dark:text-white">
                                                {{ $p->no_po ?? '—' }}
                                            </td>
                                            <td class="fi-ta-cell px-3 py-4 sm:first-of-type:ps-6 sm:last-of-type:pe-6 text-gray-500 dark:text-gray-400">
                                                {{ $p->company_name }}
                                            </td>
                                            <td class="fi-ta-cell px-3 py-4 sm:first-of-type:ps-6 sm:last-of-type:pe-6 text-right">
                                                <x-filament::button size="sm" color="gray" icon="heroicon-m-eye" wire:click="mountAction('viewPesanan', { pesanan_id: {{ $p->id }} })">
                                                    Detail
                                                </x-filament::button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                                Tidak ada data pesanan.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach

                {{-- Pagination yang Menyatu --}}
                @if($this->roleProgressData->hasPages())
                    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-4 sm:px-6">
                        <x-filament::pagination :paginator="$this->roleProgressData" :page-options="[1, 3, 5]" />
                    </div>
                @endif

            </div>

        </div>

    </div>

    <x-filament-actions::modals />
</x-filament-panels::page>