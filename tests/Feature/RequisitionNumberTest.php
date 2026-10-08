<?php

use App\Models\Keranjang;
use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('Pesanan::generateRandomAlphaNumericCode generates 4 uppercase alphanumeric characters with letters and numbers', function () {
    for ($i = 0; $i < 20; $i++) {
        $code = Pesanan::generateRandomAlphaNumericCode(4);

        expect(strlen($code))->toBe(4)
            ->and(preg_match('/^[A-Z0-9]{4}$/', $code))->toBe(1)
            ->and(preg_match('/[A-Z]/', $code))->toBe(1)
            ->and(preg_match('/[0-9]/', $code))->toBe(1);
    }
});

test('Pesanan::generateRequisitionNumber generates unique number with date formula and random alphanumeric code', function () {
    $todayPrefix = date('ymd');

    $requisitionNumber = Pesanan::generateRequisitionNumber();

    expect($requisitionNumber)->toMatch('/^\d{6}-[A-Z0-9]{4}$/')
        ->and($requisitionNumber)->toStartWith("{$todayPrefix}-");
});

test('Pesanan::generateRequisitionNumber supports custom date and no-separator format', function () {
    $customDate = '2026-09-06';

    $withSeparator = Pesanan::generateRequisitionNumber($customDate, true);
    $withoutSeparator = Pesanan::generateRequisitionNumber($customDate, false);

    expect($withSeparator)->toMatch('/^260906-[A-Z0-9]{4}$/')
        ->and($withoutSeparator)->toMatch('/^260906[A-Z0-9]{4}$/');
});

test('Pesanan::generateRequisitionNumber avoids collision with existing database records', function () {
    $user = User::factory()->create();
    $keranjang = Keranjang::create([
        'user_id' => $user->id,
        'sub_total' => 100000,
    ]);

    $existingRequisition = Pesanan::generateRequisitionNumber();

    Pesanan::create([
        'user_id' => $user->id,
        'keranjang_id' => $keranjang->id,
        'code' => 'PO-REQ-COLLISION',
        'no_requisition' => $existingRequisition,
        'total_harga' => 100000,
    ]);

    $newRequisition = Pesanan::generateRequisitionNumber();

    expect($newRequisition)->not->toBe($existingRequisition)
        ->and(Pesanan::where('no_requisition', $newRequisition)->exists())->toBeFalse();
});

test('Pesanan can store generated requisition number and display it properly', function () {
    $user = User::factory()->create();
    $keranjang = Keranjang::create([
        'user_id' => $user->id,
        'sub_total' => 150000,
    ]);

    $pesanan = Pesanan::create([
        'user_id' => $user->id,
        'keranjang_id' => $keranjang->id,
        'code' => 'PO-TEST-REQ',
        'tanggal_po' => '2026-09-06',
        'total_harga' => 150000,
    ]);

    $generatedNoReq = Pesanan::generateRequisitionNumber($pesanan->tanggal_po);
    $pesanan->update([
        'no_requisition' => $generatedNoReq,
        'status_perilisan_dana' => 1,
    ]);

    expect($pesanan->fresh()->no_requisition)->toBe($generatedNoReq)
        ->and($pesanan->fresh()->no_requisition)->toStartWith('260906-');
});

test('fixExistingRequisitionNumbers updates old format requisition numbers to unique date and alphanumeric format', function () {
    $user = User::factory()->create();
    $keranjang1 = Keranjang::create(['user_id' => $user->id, 'sub_total' => 100000]);
    $keranjang2 = Keranjang::create(['user_id' => $user->id, 'sub_total' => 100000]);

    // Old format: duplicate date only '261007'
    $pesanan1 = Pesanan::create([
        'user_id' => $user->id,
        'keranjang_id' => $keranjang1->id,
        'code' => 'PO-OLD-1',
        'no_requisition' => '261007',
        'total_harga' => 100000,
    ]);

    $pesanan2 = Pesanan::create([
        'user_id' => $user->id,
        'keranjang_id' => $keranjang2->id,
        'code' => 'PO-OLD-2',
        'no_requisition' => '261007',
        'total_harga' => 100000,
    ]);

    $stats = Pesanan::fixExistingRequisitionNumbers();

    expect($stats['total_scanned'])->toBe(2)
        ->and($stats['updated'])->toBe(2)
        ->and($stats['skipped'])->toBe(0);

    $pesanan1Fresh = $pesanan1->fresh();
    $pesanan2Fresh = $pesanan2->fresh();

    expect($pesanan1Fresh->no_requisition)->toMatch('/^261007-[A-Z0-9]{4}$/')
        ->and($pesanan2Fresh->no_requisition)->toMatch('/^261007-[A-Z0-9]{4}$/')
        ->and($pesanan1Fresh->no_requisition)->not->toBe($pesanan2Fresh->no_requisition);
});

test('artisan pesanan:fix-requisition-numbers command executes and updates old records', function () {
    $user = User::factory()->create();
    $keranjang = Keranjang::create(['user_id' => $user->id, 'sub_total' => 100000]);

    $pesanan = Pesanan::create([
        'user_id' => $user->id,
        'keranjang_id' => $keranjang->id,
        'code' => 'PO-OLD-CMD',
        'no_requisition' => '261006',
        'total_harga' => 100000,
    ]);

    $this->artisan('pesanan:fix-requisition-numbers')
        ->expectsOutputToContain('Selesai!')
        ->assertSuccessful();

    expect($pesanan->fresh()->no_requisition)->toMatch('/^261006-[A-Z0-9]{4}$/');
});
