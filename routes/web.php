<?php

use App\Http\Controllers\Dokumen\InvoicePermintaanFinanceControllers;
use App\Http\Controllers\Dokumen\SuratJalanControllers;
use App\Http\Controllers\Dokumen\SuratPOControllers;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('dokumen')->group(function () {

    Route::controller(SuratPOControllers::class)->group(function () {
        Route::get('/surat-po/{id}', 'index')->name('surat_po.index');
    });
    
    Route::controller(SuratJalanControllers::class)->group(function () {
        Route::get('/surat-jalan/{id}', 'index')->name('surat_jalan.index');;
    });

    Route::controller(InvoicePermintaanFinanceControllers::class)->group(function () {
        Route::get('/surat-invoice-permintaan-finance/{id}', 'index')->name('invoice.request.finance.index');;
    });

});

