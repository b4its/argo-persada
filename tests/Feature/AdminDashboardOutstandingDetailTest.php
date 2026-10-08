<?php

use App\Filament\Pages\Admin\AdminDashboard;
use App\Models\CompanyInternal;
use App\Models\Keranjang;
use App\Models\Pesanan;
use App\Models\QueueKeranjang;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('admin dashboard loads outstanding deliveries with items and excludes completed deliveries', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'name' => 'Admin Operational',
    ]);
    $this->actingAs($admin);

    $company = CompanyInternal::create([
        'name' => 'PT Andalan Agro Persada',
        'singkatan' => 'AAP',
        'alamat' => 'Samarinda',
    ]);

    // Order 1: Outstanding delivery (tanggal_surat_kembali is null)
    $keranjang1 = Keranjang::create([
        'user_id' => $admin->id,
        'sub_total' => 500000,
    ]);
    QueueKeranjang::create([
        'user_id' => $admin->id,
        'keranjang_id' => $keranjang1->id,
        'kode' => 'BRG-01',
        'item_name' => 'Pupuk NPK 50kg',
        'quantity' => 15,
        'satuan' => 'Sak',
        'modal' => 30000,
        'po' => 35000,
        'sub_total' => 525000,
        'supplier_name' => 'Supplier Petrokimia',
    ]);
    $orderPendingDelivery = Pesanan::create([
        'user_id' => $admin->id,
        'keranjang_id' => $keranjang1->id,
        'company_internal_id' => $company->id,
        'code' => 'ORD-DELIV-01',
        'no_po' => 'PO-DELIV-01',
        'company_name' => 'PT Perkebunan Sawit',
        'address' => 'Jl. Kebun Raya No. 10',
        'tanggal_po' => '2026-10-01',
        'tanggal_surat_kembali' => null,
        'status_pesanan' => 6,
        'total_harga' => 525000,
    ]);

    // Order 2: Completed delivery (tanggal_surat_kembali is NOT null)
    $keranjang2 = Keranjang::create([
        'user_id' => $admin->id,
        'sub_total' => 200000,
    ]);
    QueueKeranjang::create([
        'user_id' => $admin->id,
        'keranjang_id' => $keranjang2->id,
        'kode' => 'BRG-02',
        'item_name' => 'Pestisida Cair',
        'quantity' => 5,
        'satuan' => 'Botol',
        'modal' => 40000,
        'po' => 45000,
        'sub_total' => 225000,
    ]);
    $orderCompletedDelivery = Pesanan::create([
        'user_id' => $admin->id,
        'keranjang_id' => $keranjang2->id,
        'company_internal_id' => $company->id,
        'code' => 'ORD-DELIV-DONE',
        'no_po' => 'PO-DELIV-DONE',
        'company_name' => 'PT Mitra Tani',
        'address' => 'Jl. Tani Makmur',
        'tanggal_po' => '2026-10-01',
        'tanggal_surat_kembali' => '2026-10-05',
        'status_pesanan' => 8,
        'total_harga' => 225000,
    ]);

    $component = Livewire::test(AdminDashboard::class);

    $component->assertSet('totalOutstandingDeliveriesCount', 1);
    $component->assertSet('totalOutstandingItemsCount', 15);
    $deliveries = $component->get('outstandingDeliveries');
    expect($deliveries)->toHaveCount(1);
    expect($deliveries[0]['no_po'])->toBe('PO-DELIV-01');
    expect($deliveries[0]['company_name'])->toBe('PT Perkebunan Sawit');
    expect($deliveries[0]['goods'])->toHaveCount(1);
    expect($deliveries[0]['goods'][0]['nama'])->toBe('Pupuk NPK 50kg');
    expect($deliveries[0]['goods'][0]['qty'])->toBe(15);
});

test('admin dashboard loads outstanding invoices and overdue tracking', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'name' => 'Admin Finance Monitor',
    ]);
    $this->actingAs($admin);

    $company = CompanyInternal::create([
        'name' => 'PT Andalan Agro Persada',
        'singkatan' => 'AAP',
        'alamat' => 'Samarinda',
    ]);

    $keranjang = Keranjang::create([
        'user_id' => $admin->id,
        'sub_total' => 1000000,
    ]);

    // Unpaid invoice, overdue (due date in past)
    Pesanan::create([
        'user_id' => $admin->id,
        'keranjang_id' => $keranjang->id,
        'company_internal_id' => $company->id,
        'code' => 'ORD-INV-01',
        'no_po' => 'PO-INV-01',
        'no_invoice' => 'INV/AAP-FIN/IX/2026/0001',
        'company_name' => 'PT Agrikultur Kaltim',
        'tanggal_po' => '2026-09-01',
        'tanggal_terbit_invoice' => '2026-09-05',
        'tanggal_jatuh_tempo' => Carbon::now()->subDays(5)->toDateString(),
        'tanggal_lunas' => null,
        'status_pesanan' => 4,
        'total_harga' => 1500000,
    ]);

    // Unpaid invoice, not overdue (due date in future)
    Pesanan::create([
        'user_id' => $admin->id,
        'keranjang_id' => $keranjang->id,
        'company_internal_id' => $company->id,
        'code' => 'ORD-INV-02',
        'no_po' => 'PO-INV-02',
        'no_invoice' => 'INV/AAP-FIN/X/2026/0002',
        'company_name' => 'PT Sukses Makmur',
        'tanggal_po' => '2026-10-01',
        'tanggal_terbit_invoice' => '2026-10-02',
        'tanggal_jatuh_tempo' => Carbon::now()->addDays(10)->toDateString(),
        'tanggal_lunas' => null,
        'status_pesanan' => 4,
        'total_harga' => 2500000,
    ]);

    // Paid invoice (should be excluded)
    Pesanan::create([
        'user_id' => $admin->id,
        'keranjang_id' => $keranjang->id,
        'company_internal_id' => $company->id,
        'code' => 'ORD-INV-PAID',
        'no_po' => 'PO-INV-PAID',
        'no_invoice' => 'INV/AAP-FIN/X/2026/0003',
        'company_name' => 'PT Mandiri Tani',
        'tanggal_po' => '2026-10-01',
        'tanggal_terbit_invoice' => '2026-10-02',
        'tanggal_jatuh_tempo' => '2026-10-15',
        'tanggal_lunas' => '2026-10-05',
        'status_pesanan' => 5,
        'total_harga' => 800000,
    ]);

    $component = Livewire::test(AdminDashboard::class);

    $component->assertSet('totalOutstandingInvoicesCount', 2);
    $component->assertSet('totalOutstandingInvoicesNominal', 4000000.0);
    $component->assertSet('totalOverdueInvoicesCount', 1);

    $invoices = $component->get('outstandingInvoices');
    expect($invoices)->toHaveCount(2);

    // Overdue item check
    $overdueItem = collect($invoices)->firstWhere('no_invoice', 'INV/AAP-FIN/IX/2026/0001');
    expect($overdueItem['tempo_color'])->toBe('danger');
    expect($overdueItem['tempo_label'])->toContain('Terlambat');

    // Future item check
    $futureItem = collect($invoices)->firstWhere('no_invoice', 'INV/AAP-FIN/X/2026/0002');
    expect($futureItem['tempo_color'])->toBe('success');
    expect($futureItem['tempo_label'])->toContain('Sisa');
});

test('admin dashboard provides complete order status distribution across stages', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'name' => 'Admin Metrics',
    ]);
    $this->actingAs($admin);

    $keranjang = Keranjang::create([
        'user_id' => $admin->id,
        'sub_total' => 100000,
    ]);

    // Create 1 order in status 0 and 2 orders in status 3
    Pesanan::create([
        'user_id' => $admin->id,
        'keranjang_id' => $keranjang->id,
        'code' => 'PO-ST-0',
        'status_pesanan' => 0,
        'total_harga' => 100000,
    ]);
    Pesanan::create([
        'user_id' => $admin->id,
        'keranjang_id' => $keranjang->id,
        'code' => 'PO-ST-3A',
        'status_pesanan' => 3,
        'total_harga' => 100000,
    ]);
    Pesanan::create([
        'user_id' => $admin->id,
        'keranjang_id' => $keranjang->id,
        'code' => 'PO-ST-3B',
        'status_pesanan' => 3,
        'total_harga' => 100000,
    ]);

    $component = Livewire::test(AdminDashboard::class);

    $statusMetrics = $component->get('orderStatusMetrics');
    expect($statusMetrics['total_all'])->toBe(3);

    $stage0 = collect($statusMetrics['items'])->firstWhere('code', 0);
    expect($stage0['count'])->toBe(1);
    expect($stage0['percentage'])->toBe(33.3);

    $stage3 = collect($statusMetrics['items'])->firstWhere('code', 3);
    expect($stage3['count'])->toBe(2);
    expect($stage3['percentage'])->toBe(66.7);
});

test('admin dashboard allows operational tab switching and renders operational tables in view', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'name' => 'Admin Tabs',
    ]);
    $this->actingAs($admin);

    $keranjang = Keranjang::create([
        'user_id' => $admin->id,
        'sub_total' => 100000,
    ]);
    QueueKeranjang::create([
        'user_id' => $admin->id,
        'keranjang_id' => $keranjang->id,
        'kode' => 'BRG-TAB',
        'item_name' => 'Herbisida 1L',
        'quantity' => 8,
        'satuan' => 'Botol',
        'modal' => 20000,
        'po' => 25000,
        'sub_total' => 200000,
    ]);

    Pesanan::create([
        'user_id' => $admin->id,
        'keranjang_id' => $keranjang->id,
        'code' => 'ORD-TAB-01',
        'no_po' => 'PO-TAB-01',
        'company_name' => 'PT Tab Testing',
        'no_invoice' => 'INV-TAB-01',
        'tanggal_surat_kembali' => null,
        'tanggal_lunas' => null,
        'status_pesanan' => 4,
        'total_harga' => 200000,
    ]);

    $component = Livewire::test(AdminDashboard::class);

    $component->assertSee('Outstanding Pengiriman')
        ->assertSee('Outstanding Tagihan')
        ->assertSee('Status Pemesanan Detail')
        ->assertSee('PT Tab Testing')
        ->assertSee('Herbisida 1L');

    // Switch tab to delivery
    $component->call('setOperationalTab', 'delivery');
    $component->assertSet('activeOperationalTab', 'delivery');

    // Switch tab to invoice
    $component->call('setOperationalTab', 'invoice');
    $component->assertSet('activeOperationalTab', 'invoice');

    // Switch tab to status
    $component->call('setOperationalTab', 'status');
    $component->assertSet('activeOperationalTab', 'status');
});
