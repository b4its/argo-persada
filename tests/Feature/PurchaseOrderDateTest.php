<?php

use App\Models\CompanyInternal;
use App\Models\Keranjang;
use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

test('pesanan can store and retrieve backdated tanggal_po', function () {
    $user = User::factory()->create();
    $keranjang = Keranjang::create([
        'user_id' => $user->id,
        'sub_total' => 100000,
    ]);

    $backdate = '2026-09-06';

    $pesanan = Pesanan::create([
        'user_id' => $user->id,
        'keranjang_id' => $keranjang->id,
        'code' => 'PO-TEST-001',
        'no_po' => 'PO-TEST-001',
        'tanggal_po' => $backdate,
        'total_harga' => 111000,
        'company_name' => 'PT Agropersada Mitra',
    ]);

    expect($pesanan->fresh()->tanggal_po->format('Y-m-d'))->toBe('2026-09-06')
        ->and($pesanan->fresh()->effective_tanggal_po)->toBe('06-09-2026');
});

test('surat_po view displays backdated po date', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $keranjang = Keranjang::create([
        'user_id' => $user->id,
        'sub_total' => 50000,
    ]);

    $pesanan = Pesanan::create([
        'user_id' => $user->id,
        'keranjang_id' => $keranjang->id,
        'code' => 'PO-BACKDATE-01',
        'no_po' => 'PO-BACKDATE-01',
        'tanggal_po' => '2026-09-06',
        'total_harga' => 55500,
        'company_name' => 'PT Perkebunan Sukses',
    ]);

    $response = $this->get(route('surat_po.index', ['id' => $pesanan->id]));

    $response->assertStatus(200);
    $response->assertSee('06-09-2026');
});
