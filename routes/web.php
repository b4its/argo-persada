<?php

use App\Http\Controllers\Dokumen\BukuBMutasi;
use App\Http\Controllers\Dokumen\InvoicePermintaanFinanceControllers;
use App\Http\Controllers\Dokumen\KasHarian\BukuBesarController;
use App\Http\Controllers\Dokumen\KasHarianController;
use App\Http\Controllers\Dokumen\SuratJalanControllers;
use App\Http\Controllers\Dokumen\SuratPesanan;
use App\Http\Controllers\Dokumen\SuratPOControllers;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::prefix('dokumen')->group(function () {

    Route::controller(SuratPesanan::class)->group(function () {
        Route::get('/surat-pesanan', 'index')->name('surat_pesanan.index');
        Route::get('/surat-pesanan/excel', 'exportExcel')->name('export.surat.pesanan');
    });

    Route::controller(SuratPOControllers::class)->group(function () {
        Route::get('/surat-po/{id}/excel', 'exportExcel')->name('export.surat_po');
        Route::get('/surat-po/{id}', 'index')->name('surat_po.index');
    });
    
    Route::controller(SuratJalanControllers::class)->group(function () {
        Route::get('/surat-jalan/{id}', 'index')->name('surat_jalan.index');
    });

    Route::controller(InvoicePermintaanFinanceControllers::class)->group(function () {
        Route::get('/surat-invoice-permintaan-finance/{id}', 'index')->name('invoice.request.finance.index');
    });

    Route::controller(BukuBMutasi::class)->group(function () {
        Route::get('/buku-besar-mutasi/{id}', 'index')->name('buku_besar_mutasi.index');
    });
    
    Route::controller(KasHarianController::class)->group(function () {
        Route::get('/kas-harian/excel', 'exportExcel')->name('export.kas_harian_all');
        Route::get('/kas-harian/{id}', 'index')->name('kas_harian.index');
        Route::get('/kas-harian', 'index_all')->name('kas_harian_all.index');
    });
    Route::controller(BukuBesarController::class)->group(function () {
        Route::get('/buku-besar/excel', 'exportExcel')->name('export.buku_besar');
        Route::get('/buku-besar', 'index')->name('buku_besar.index');
    });
    
    

});

