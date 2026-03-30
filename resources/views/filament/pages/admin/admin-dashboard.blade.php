<x-filament-panels::page>

<div class="flex flex-col gap-6">

{{-- ╔══════════════════════════════╗
             ║   STAT CARDS (Modern Style)  ║
             ╚══════════════════════════════╝ --}}
        {{-- Menggunakan inline CSS Grid agar layout menyamping 3 kolom terjamin bekerja --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
            
            {{-- Total Pesanan --}}
            <x-filament::section>
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    {{-- Icon Wrapper --}}
                    <div style="display: flex; align-items: center; justify-content: center; width: 3rem; height: 3rem; border-radius: 0.75rem; background-color: rgba(99, 102, 241, 0.1); color: rgb(79, 70, 229);">
                        <x-filament::icon icon="heroicon-o-shopping-bag" style="width: 1.5rem; height: 1.5rem;" />
                    </div>
                    {{-- Trend Badge menggunakan Native Filament --}}
                    <x-filament::badge color="success" size="sm" icon="heroicon-m-arrow-trending-up">
                        12.5%
                    </x-filament::badge>
                </div>
                
                <div style="margin-top: 1rem;">
                    <h3 style="font-size: 0.875rem; font-weight: 600; color: #6b7280;">Total Pesanan</h3>
                    <p style="margin-top: 0.25rem; font-size: 1.875rem; font-weight: 800;">
                        {{ $this->stats['total_pesanan'] }}
                    </p>
                </div>
                
                {{-- Decorative Progress Bar --}}
                <div style="margin-top: 1.5rem; width: 100%; height: 0.375rem; border-radius: 9999px; background-color: rgba(156, 163, 175, 0.2); overflow: hidden;">
                    <div style="height: 100%; border-radius: 9999px; width: 65%; background-color: rgb(99, 102, 241);"></div>
                </div>
            </x-filament::section>

            {{-- Task Aktif --}}
            <x-filament::section>
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div style="display: flex; align-items: center; justify-content: center; width: 3rem; height: 3rem; border-radius: 0.75rem; background-color: rgba(245, 158, 11, 0.1); color: rgb(217, 119, 6);">
                        <x-filament::icon icon="heroicon-o-clock" style="width: 1.5rem; height: 1.5rem;" />
                    </div>
                    <x-filament::badge color="danger" size="sm" icon="heroicon-m-arrow-trending-down">
                        5.2%
                    </x-filament::badge>
                </div>
                
                <div style="margin-top: 1rem;">
                    <h3 style="font-size: 0.875rem; font-weight: 600; color: #6b7280;">Task Aktif</h3>
                    <p style="margin-top: 0.25rem; font-size: 1.875rem; font-weight: 800;">
                        {{ $this->stats['active_tasks'] }}
                    </p>
                </div>
                
                <div style="margin-top: 1.5rem; width: 100%; height: 0.375rem; border-radius: 9999px; background-color: rgba(156, 163, 175, 0.2); overflow: hidden;">
                    <div style="height: 100%; border-radius: 9999px; width: 45%; background-color: rgb(245, 158, 11);"></div>
                </div>
            </x-filament::section>

            {{-- Task Selesai --}}
            <x-filament::section>
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div style="display: flex; align-items: center; justify-content: center; width: 3rem; height: 3rem; border-radius: 0.75rem; background-color: rgba(16, 185, 129, 0.1); color: rgb(5, 150, 105);">
                        <x-filament::icon icon="heroicon-o-check-circle" style="width: 1.5rem; height: 1.5rem;" />
                    </div>
                    <x-filament::badge color="success" size="sm" icon="heroicon-m-arrow-trending-up">
                        8.4%
                    </x-filament::badge>
                </div>
                
                <div style="margin-top: 1rem;">
                    <h3 style="font-size: 0.875rem; font-weight: 600; color: #6b7280;">Task Selesai</h3>
                    <p style="margin-top: 0.25rem; font-size: 1.875rem; font-weight: 800;">
                        {{ $this->stats['completed_tasks'] }}
                    </p>
                </div>
                
                <div style="margin-top: 1.5rem; width: 100%; height: 0.375rem; border-radius: 9999px; background-color: rgba(156, 163, 175, 0.2); overflow: hidden;">
                    <div style="height: 100%; border-radius: 9999px; width: 80%; background-color: rgb(16, 185, 129);"></div>
                </div>
            </x-filament::section>

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
                            <div class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                                
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