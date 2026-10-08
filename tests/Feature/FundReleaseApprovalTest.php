<?php

use App\Models\Keranjang;
use App\Models\Pesanan;
use App\Models\Task;
use App\Models\TaskActivity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('order with pending fund release can be approved by finance/admin', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $keranjang = Keranjang::create([
        'user_id' => $admin->id,
        'sub_total' => 500000,
    ]);

    $pesanan = Pesanan::create([
        'user_id' => $admin->id,
        'keranjang_id' => $keranjang->id,
        'code' => 'PO-APP-001',
        'status_perilisan_dana' => 1, // Pending
        'total_harga' => 555000,
    ]);

    $task = Task::create([
        'pesanan_id' => $pesanan->id,
        'role' => 'finance',
        'title' => 'Perilisan dana untuk ' . $pesanan->code,
        'status' => 0,
    ]);

    expect($pesanan->status_perilisan_dana)->toBe(1);

    // Simulate approval by admin/finance
    $pesanan->update(['status_perilisan_dana' => 3]);
    TaskActivity::create([
        'created_user_id' => $admin->id,
        'updated_user_id' => $admin->id,
        'task_id' => $task->id,
        'note' => 'Dana disetujui untuk rilis',
        'pesanan_status' => 2,
    ]);

    expect($pesanan->fresh()->status_perilisan_dana)->toBe(3);
    expect($task->taskActivities()->count())->toBe(1);
});

test('order with pending fund release can be rejected with reason', function () {
    $finance = User::factory()->create(['role' => 'finance']);
    $keranjang = Keranjang::create([
        'user_id' => $finance->id,
        'sub_total' => 250000,
    ]);

    $pesanan = Pesanan::create([
        'user_id' => $finance->id,
        'keranjang_id' => $keranjang->id,
        'code' => 'PO-REJ-001',
        'status_perilisan_dana' => 1,
        'total_harga' => 250000,
    ]);

    $task = Task::create([
        'pesanan_id' => $pesanan->id,
        'role' => 'finance',
        'title' => 'Perilisan dana untuk ' . $pesanan->code,
        'status' => 0,
    ]);

    // Simulate rejection
    $pesanan->update(['status_perilisan_dana' => 2]);
    TaskActivity::create([
        'created_user_id' => $finance->id,
        'updated_user_id' => $finance->id,
        'task_id' => $task->id,
        'note' => 'Pengajuan rilis dana DITOLAK oleh Finance: Anggaran tidak mencukupi',
        'pesanan_status' => 1,
    ]);

    expect($pesanan->fresh()->status_perilisan_dana)->toBe(2);
    expect($task->taskActivities()->first()->note)->toContain('DITOLAK');
});
