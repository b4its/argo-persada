<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('pesanan:fix-requisition-numbers', function () {
    $this->info('Memperbaiki nomor requisition pada record pesanan lama di database...');
    $stats = \App\Models\Pesanan::fixExistingRequisitionNumbers();
    $this->info("Selesai! Total discan: {$stats['total_scanned']}, Diperbarui: {$stats['updated']}, Dilewati: {$stats['skipped']}");
})->purpose('Perbaiki nomor requisition lama di database menjadi format tahun, bulan, hari + random alfanumerik (contoh: 261008-AC7X)');

