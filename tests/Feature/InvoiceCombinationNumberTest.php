<?php

use App\Models\CompanyInternal;
use App\Models\Keranjang;
use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('pesanan generates combination invoice number format', function () {
    $company = CompanyInternal::create([
        'name' => 'PT Andalan Agro Persada',
        'singkatan' => 'AAP',
        'alamat' => 'Samarinda',
    ]);

    $user = User::factory()->create();
    $keranjang = Keranjang::create([
        'user_id' => $user->id,
        'sub_total' => 1000000,
    ]);

    $pesanan = Pesanan::create([
        'id' => 42,
        'user_id' => $user->id,
        'keranjang_id' => $keranjang->id,
        'company_internal_id' => $company->id,
        'code' => 'PO-TEST-42',
        'total_harga' => 1110000,
        'tipe_pesanan' => 0,
    ]);

    $invoiceNumber = $pesanan->generateCombinationInvoiceNumber();

    $year = date('Y');
    $seq = sprintf('%04d', $pesanan->id);
    expect($invoiceNumber)->toStartWith('INV/AAP-FIN/')
        ->and($invoiceNumber)->toContain("/{$year}/{$seq}");
});

test('pesanan can store combination invoice number and update status', function () {
    $user = User::factory()->create();
    $keranjang = Keranjang::create([
        'user_id' => $user->id,
        'sub_total' => 200000,
    ]);

    $pesanan = Pesanan::create([
        'id' => 99,
        'user_id' => $user->id,
        'keranjang_id' => $keranjang->id,
        'code' => 'PO-TEST-99',
        'total_harga' => 200000,
    ]);

    $customInv = 'INV/AAP-FIN/X/2026/0099';
    $pesanan->update([
        'no_invoice' => $customInv,
        'tanggal_terbit_invoice' => now(),
        'tanggal_jatuh_tempo' => now()->addDays(30),
    ]);

    expect($pesanan->fresh()->no_invoice)->toBe('INV/AAP-FIN/X/2026/0099');
});
