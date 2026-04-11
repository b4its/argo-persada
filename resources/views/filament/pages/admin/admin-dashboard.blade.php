<x-filament-panels::page>

<div class="flex flex-col gap-6">


        {{-- ╔══════════════════════════════════════════════════╗
             ║   MAIN CONTENT                                   ║
             ╚══════════════════════════════════════════════════╝ --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">



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