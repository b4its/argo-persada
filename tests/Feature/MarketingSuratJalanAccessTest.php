<?php

use App\Models\CompanyInternal;
use App\Models\Keranjang;
use App\Models\Pesanan;
use App\Models\QueueKeranjang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('marketing user can access and view surat jalan document', function () {
    $marketingUser = User::factory()->create([
        'role' => 'marketing',
        'name' => 'Marketing Officer',
    ]);
    $this->actingAs($marketingUser);

    $company = CompanyInternal::create([
        'name' => 'PT Andalan Agro Persada',
        'singkatan' => 'AAP',
        'alamat' => 'Samarinda',
    ]);

    $keranjang = Keranjang::create([
        'user_id' => $marketingUser->id,
        'sub_total' => 300000,
    ]);

    QueueKeranjang::create([
        'user_id' => $marketingUser->id,
        'keranjang_id' => $keranjang->id,
        'kode' => 'BRG-01',
        'item_name' => 'Pupuk Urea 50kg',
        'quantity' => 10,
        'satuan' => 'Sak',
        'modal' => 25000,
        'po' => 30000,
        'sub_total' => 300000,
    ]);

    $pesanan = Pesanan::create([
        'user_id' => $marketingUser->id,
        'keranjang_id' => $keranjang->id,
        'company_internal_id' => $company->id,
        'code' => 'PO-MKT-SJ-01',
        'no_po' => 'PO-MKT-SJ-01',
        'total_harga' => 333000,
        'company_name' => 'PT Pelanggan Sejahtera',
        'tanggal_terbit_surat_jalan' => '2026-10-08',
        'no_delivery_order' => 'DO-20261008-001',
    ]);

    $backUrl = route('filament.marketing.resources.pesanan.index');
    $response = $this->get(route('surat_jalan.index', [
        'id' => $pesanan->id,
        'back' => $backUrl,
    ]));

    $response->assertStatus(200);
    $response->assertSee('SURAT JALAN');
    $response->assertSee('Pupuk Urea 50kg');
    $response->assertSee('PT Pelanggan Sejahtera');
    $response->assertSee($backUrl);
});
