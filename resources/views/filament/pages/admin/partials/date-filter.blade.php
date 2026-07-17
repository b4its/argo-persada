@php
    $filterPreset = session('dashboard_filter_preset', '');
    $filterStartDate = session('dashboard_filter_start_date', '');
    $filterEndDate = session('dashboard_filter_end_date', '');
@endphp

<div class="px-4 sm:px-6 lg:px-8 pt-4">
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-4">
        <div class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Filter Waktu
                </label>
                <select wire:model.live="filterPreset"
                        class="block min-w-[200px] rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white text-sm focus:ring-primary-500 focus:border-primary-500 shadow-sm">
                    <option value="">Semua Data</option>
                    <option value="7days">7 Hari Terakhir</option>
                    <option value="2weeks">2 Minggu Terakhir</option>
                    <option value="3weeks">3 Minggu Terakhir</option>
                    <option value="1month">1 Bulan Terakhir</option>
                    <option value="custom">Kustom</option>
                </select>
            </div>
            @if($filterPreset === 'custom')
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Dari Tanggal
                    </label>
                    <input type="date" wire:model.blur="filterStartDate"
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white text-sm focus:ring-primary-500 focus:border-primary-500 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Sampai Tanggal
                    </label>
                    <input type="date" wire:model.blur="filterEndDate"
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white text-sm focus:ring-primary-500 focus:border-primary-500 shadow-sm">
                </div>
                <div>
                    <x-filament::button color="primary" size="sm" wire:click="applyCustomFilter">
                        Terapkan
                    </x-filament::button>
                </div>
            @endif
            @if($filterPreset !== '' && $filterPreset !== 'custom')
                <div class="flex items-center pb-1">
                    <span class="text-sm text-gray-500">
                        {{ $filterStartDate ? \Carbon\Carbon::parse($filterStartDate)->format('d M Y') : '' }} -
                        {{ $filterEndDate ? \Carbon\Carbon::parse($filterEndDate)->format('d M Y') : '' }}
                    </span>
                </div>
            @endif
        </div>
    </div>
</div>
