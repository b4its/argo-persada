<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    const ROLES = ['admin', 'marketing', 'finance', 'logistik', 'guest'];
    const ROLE_COUNTS = [5, 30, 20, 30, 15];
    const BANK_NAMES = ['BCA', 'Mandiri', 'BNI', 'BRI', 'CIMB Niaga', 'Danamon', 'Permata', 'BTN'];
    const SUPPLIERS = ['PT. Sumber Makmur', 'CV. Jaya Abadi', 'UD. Berkah', 'PT. Indah Jaya', 'CV. Karya Mandiri', 'UD. Sejahtera', 'PT. Bumi Sentosa', 'CV. Agung Perkasa'];
    const ITEM_NAMES = ['Besi Beton 12mm', 'Semen 50kg', 'Pasir Bangka', 'Bata Merah', 'Cat Tembok 5kg', 'Paku 10cm', 'Kayu Balok 6x12', 'Pipa PVC 4 inch', 'Kabel Listrik 2.5mm', 'Lampu LED 20W', 'Keramik 60x60', 'Triplek 18mm', 'Atap Spandek', 'Gypsum Board 9mm', 'Hollow Baja Ringan'];

    public function run(): void
    {
        $this->command->info('Seeding 100 users + 200 pesanan with workflow...');
        $start = microtime(true);

        $userIdMap = $this->seedUsers();
        $this->seedProfiles($userIdMap);
        $companyIds = $this->seedCompanyInternal();
        $akunIds = $this->seedAkunKeuangan();

        [$marketingIds, $adminIds, $financeIds, $logistikIds] = $this->getUsersByRole($userIdMap);
        $pesananIds = $this->seedPesanan($userIdMap, $marketingIds, $adminIds, $financeIds, $logistikIds, $companyIds);

        $this->seedAwk($akunIds);
        $this->seedKasHarian($userIdMap, $companyIds, $akunIds, $pesananIds);

        $elapsed = round(microtime(true) - $start, 2);
        $this->command->info("Seeding selesai dalam {$elapsed} detik!");
    }

    protected function getUsersByRole(array $userIds): array
    {
        $users = DB::table('users')->whereIn('id', $userIds)->get();
        $marketing = $users->where('role', 'marketing')->pluck('id')->toArray();
        $admin = $users->where('role', 'admin')->pluck('id')->toArray();
        $finance = $users->where('role', 'finance')->pluck('id')->toArray();
        $logistik = $users->where('role', 'logistik')->pluck('id')->toArray();
        return [$marketing, $admin, $finance, $logistik];
    }

    protected function seedUsers(): array
    {
        $this->command->info('  Membuat 100 users...');
        $users = [];
        $now = now();
        $id = 1;

        foreach (self::ROLES as $i => $role) {
            for ($j = 0; $j < self::ROLE_COUNTS[$i]; $j++) {
                $nama = fake('id_ID')->name();
                $users[] = [
                    'id' => $id,
                    'name' => $nama,
                    'email' => strtolower(str_replace(' ', '.', $nama)) . $id . '@example.com',
                    'email_verified_at' => $now,
                    'password' => Hash::make('password'),
                    'role' => $role,
                    'remember_token' => \Str::random(10),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $id++;
            }
        }

        DB::table('users')->insert($users);
        $this->command->info('    -> ' . ($id - 1) . ' users created (0 superadmin)');
        return DB::table('users')->pluck('id')->toArray();
    }

    protected function seedProfiles(array $userIds): void
    {
        $profiles = [];
        $now = now();
        foreach ($userIds as $userId) {
            $profiles[] = [
                'user_id' => $userId,
                'images_url' => null,
                'phone_number' => '08' . fake('id_ID')->numerify('##########'),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('profile')->insert($profiles);
        $this->command->info('    -> ' . count($profiles) . ' profiles auto-created');
    }

    protected function seedCompanyInternal(): array
    {
        $this->command->info('  Membuat company_internal...');
        $companies = [];
        $now = now();
        for ($i = 1; $i <= 30; $i++) {
            $companies[] = [
                'name' => fake('id_ID')->company(),
                'singkatan' => strtoupper(fake()->lexify('???')),
                'alamat' => fake('id_ID')->address(),
                'phone_number' => '021' . fake()->numerify('########'),
                'is_ppn' => fake()->boolean(70) ? 1 : 0,
                'gambar' => null,
                'no_rekening' => fake()->numerify('##############'),
                'nama_bank' => fake()->randomElement(self::BANK_NAMES),
                'nama_pemilik_bank' => fake('id_ID')->name(),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('company_internal')->insert($companies);
        return DB::table('company_internal')->pluck('id')->toArray();
    }

    protected function seedAkunKeuangan(): array
    {
        $akun = [
            ['kode' => '111', 'name' => 'Kas Besar', 'kategori' => 1],
            ['kode' => '112', 'name' => 'Kas Kecil', 'kategori' => 1],
            ['kode' => '121', 'name' => 'Piutang Usaha', 'kategori' => 2],
            ['kode' => '211', 'name' => 'Hutang Usaha', 'kategori' => 2],
            ['kode' => '311', 'name' => 'Modal', 'kategori' => null],
            ['kode' => '411', 'name' => 'Pendapatan Jasa', 'kategori' => 1],
            ['kode' => '412', 'name' => 'Pendapatan Supply', 'kategori' => 1],
            ['kode' => '511', 'name' => 'Biaya Operasional', 'kategori' => 3],
            ['kode' => '512', 'name' => 'Biaya Transport', 'kategori' => 3],
            ['kode' => '521', 'name' => 'Biaya Administrasi', 'kategori' => 4],
        ];
        $now = now();
        foreach ($akun as &$a) {
            $a['created_at'] = $now;
            $a['updated_at'] = $now;
        }
        DB::table('akun_keuangan')->insert($akun);
        return DB::table('akun_keuangan')->pluck('id')->toArray();
    }

    protected function seedAwk(array $akunIds): void
    {
        $bukuBesarData = [];
        $mutasiData = [];
        $mutasiItemData = [];
        $now = now();
        $bbPrefixes = ['BB-001', 'BB-002', 'BB-003', 'BB-004', 'BB-005'];

        for ($i = 0; $i < 30; $i++) {
            $bbId = $i + 1;
            $bukuBesarData[] = [
                'id_pesanan' => null,
                'code' => 'BB-' . str_pad($bbId, 4, '0', STR_PAD_LEFT),
                'name' => fake()->randomElement(['Kas', 'Bank', 'Piutang', 'Hutang', 'Modal', 'Pendapatan', 'Biaya']),
                'type' => fake()->randomElement(['Debet', 'Kredit']),
                'periode' => fake()->date('Y-m-d'),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $mutId = $i + 1;
            $saldoAwal = fake()->randomFloat(2, 0, 10000000);
            $mutasiData[] = [
                'id_buku_Besar' => $bbId,
                'code' => 'MUT-' . str_pad($mutId, 4, '0', STR_PAD_LEFT),
                'name' => fake()->sentence(3),
                'saldo_awal' => $saldoAwal,
                'saldo_akhir' => $saldoAwal + fake()->randomFloat(2, -5000000, 5000000),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $debet = fake()->randomFloat(2, 0, 5000000);
            $kredit = fake()->randomFloat(2, 0, 5000000);
            $mutasiItemData[] = [
                'id_mutasi' => $mutId,
                'no_ref' => 'REF-' . fake()->bothify('####/??/###'),
                'keterangan' => fake()->sentence(5),
                'debet' => $debet,
                'kredit' => $kredit,
                'saldo' => $debet - $kredit,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('buku_besar')->insert($bukuBesarData);
        DB::table('mutasi')->insert($mutasiData);
        DB::table('mutasi_item')->insert($mutasiItemData);
    }

    protected function seedPesanan(array $userIds, array $marketingIds, array $adminIds, array $financeIds, array $logistikIds, array $companyIds): array
    {
        $this->command->info('  Membuat 200 pesanan dengan workflow...');

        $pesananData = [];
        $keranjangData = [];
        $queueKeranjangData = [];
        $taskData = [];
        $taskActivityData = [];
        $logData = [];
        $kasHarianData = [];
        $bukuBesarData = [];

        $now = now();
        $baseDate = Carbon::parse('2026-01-01');
        $allUserIds = $userIds;

        $keranjangId = 1;
        $queueKeranjangId = 1;
        $taskId = 1;
        $taskActivityId = 1;
        $logId = 1;
        $kasId = 1;

        for ($i = 1; $i <= 200; $i++) {
            $createdAt = $baseDate->copy()->addDays(fake()->numberBetween(0, 190));
            $marketingUser = $marketingIds[array_rand($marketingIds)];
            $tipe = fake()->randomElement([0, 1]);

            // Tentukan workflow stage
            // 0=Dibuat, 1=Pending(req), 2=Perlu Rilis Dana, 3=Perlu Cetak Invoice,
            // 4=Perlu Penagihan, 5=Ditandai Lunas, 6=Cetak Surat Jalan, 7=Selesai Dikirim, 8=Selesai
            $stageWeights = [5, 5, 10, 10, 10, 5, 10, 10, 35]; // weight distribution
            $totalWeight = array_sum($stageWeights);
            $rand = fake()->numberBetween(1, $totalWeight);
            $cumulative = 0;
            $status = 0;
            foreach ($stageWeights as $s => $w) {
                $cumulative += $w;
                if ($rand <= $cumulative) { $status = $s; break; }
            }

            // Hitung tanggal sesuai status
            $tglRequisisi = $status >= 1 ? $createdAt->copy()->addDays(fake()->numberBetween(1, 3)) : null;
            $tglRilis = $status >= 2 ? ($tglRequisisi ? $tglRequisisi->copy()->addDays(fake()->numberBetween(2, 7)) : $createdAt->copy()->addDays(5)) : null;
            $tglInvoice = $status >= 3 ? ($tglRilis ? $tglRilis->copy()->addDays(fake()->numberBetween(1, 5)) : null) : null;
            $tglJatuhTempo = $status >= 4 ? ($tglInvoice ? $tglInvoice->copy()->addDays(30) : null) : null;
            $tglSuratJalan = $status >= 6 ? ($tglInvoice ? $tglInvoice->copy()->addDays(fake()->numberBetween(1, 3)) : null) : null;
            $tglKembali = $status >= 7 && $tglSuratJalan ? $tglSuratJalan->copy()->addDays(fake()->numberBetween(3, 7)) : null;
            $tglLunas = $status >= 8 ? ($tglKembali ? $tglKembali->copy()->addDays(fake()->numberBetween(1, 14)) : null) : null;

            $totalHarga = fake()->randomFloat(2, 5000000, 500000000);
            $ppn = round($totalHarga * 0.11, 2);
            $metodeRilis = fake()->randomElement([0, 1, 2]);
            $metodeLunas = fake()->randomElement([0, 1, 2]);

            $pesananData[] = [
                'user_id' => $marketingUser,
                'keranjang_id' => $keranjangId,
                'company_internal_id' => $companyIds[array_rand($companyIds)],
                'saldo_id' => null,
                'code' => 'PSN-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'tipe_pesanan' => $tipe,
                'group_name' => fake()->randomElement(['Group A', 'Group B', 'Group C', 'Group D', null]),
                'company_name' => fake('id_ID')->company(),
                'address' => fake('id_ID')->address(),
                'ppn' => $ppn,
                'total_harga' => $totalHarga,
                'no_po' => $status >= 1 ? 'PO-' . fake()->bothify('####/??/###') : null,
                'no_requisition' => $status >= 1 ? 'REQ-' . fake()->bothify('####/??/###') : null,
                'no_invoice' => $status >= 3 ? 'INV-' . fake()->bothify('####/??/###') : null,
                'no_delivery_order' => $status >= 6 ? 'DO-' . fake()->bothify('####/??/###') : null,
                'tanggal_rilis_dana' => $tglRilis?->toDateString(),
                'tanggal_terbit_invoice' => $tglInvoice?->toDateString(),
                'tanggal_jatuh_tempo' => $tglJatuhTempo?->toDateString(),
                'tanggal_terbit_surat_jalan' => $tglSuratJalan?->toDateString(),
                'tanggal_surat_kembali' => $tglKembali?->toDateString(),
                'tanggal_lunas' => $tglLunas?->toDateString(),
                'validasi_tanggal_lunas' => $tglLunas?->toDateString(),
                'metode_pembayaran_rilis_dana' => $metodeRilis,
                'nama_bank_rilis_dana' => $metodeRilis === 2 ? fake()->randomElement(self::BANK_NAMES) : null,
                'no_rekening_rilis_dana' => $metodeRilis === 2 ? fake()->numerify('##############') : null,
                'nama_bank_lunas' => $metodeLunas === 2 ? fake()->randomElement(self::BANK_NAMES) : null,
                'no_rekening_lunas' => $metodeLunas === 2 ? fake()->numerify('##############') : null,
                'metode_pembayaran_lunas' => $metodeLunas,
                'status_pesanan' => $status,
                'status_perilisan_dana' => $status >= 2 ? fake()->randomElement([1, 2, 3]) : 0,
                'file_invoice' => $status >= 3 ? 'invoices/inv_' . $i . '.pdf' : null,
                'file_do' => $status >= 6 ? 'do/do_' . $i . '.pdf' : null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];

            // Keranjang
            $subTotal = 0;
            $numItems = fake()->numberBetween(2, 5);
            $itemSubTotals = [];
            for ($j = 0; $j < $numItems; $j++) {
                $qty = fake()->numberBetween(1, 50);
                $modal = fake()->randomFloat(2, 5000, 500000);
                $po = $modal * fake()->randomFloat(2, 1.1, 1.5);
                $subTotalItem = $qty * $po;
                $subTotal += $subTotalItem;
                $itemSubTotals[] = $subTotalItem;

                $queueKeranjangData[] = [
                    'id' => $queueKeranjangId,
                    'user_id' => $marketingUser,
                    'keranjang_id' => $keranjangId,
                    'kode' => 'ITM-' . str_pad($queueKeranjangId, 4, '0', STR_PAD_LEFT),
                    'supplier_name' => fake()->randomElement(self::SUPPLIERS),
                    'keterangan' => fake()->sentence(3),
                    'item_name' => fake()->randomElement(self::ITEM_NAMES),
                    'quantity' => $qty,
                    'satuan' => fake()->randomElement(['pcs', 'kg', 'm', 'liter', 'unit', 'sak', 'lembar']),
                    'modal' => $modal,
                    'po' => $po,
                    'sub_total' => $subTotalItem,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];
                $queueKeranjangId++;
            }

            $keranjangData[] = [
                'id' => $keranjangId,
                'user_id' => $marketingUser,
                'sub_total' => $subTotal,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];

            // Tasks per role berdasarkan workflow
            $roleTasks = [
                'marketing' => ['title' => 'Pembuatan Pesanan', 'status' => $status >= 0 ? min(2, $status >= 1 ? 2 : $status) : 0],
                'admin' => ['title' => 'Validasi & Approve', 'status' => $status >= 2 ? 2 : ($status >= 1 ? 1 : 0)],
                'finance' => ['title' => 'Rilis Dana & Penagihan', 'status' => $status >= 3 ? 2 : ($status >= 2 ? 1 : 0)],
                'logistik' => ['title' => 'Cetak Surat Jalan & Kirim', 'status' => $status >= 6 ? 2 : ($status >= 3 ? 1 : 0)],
            ];

            $assignedUsers = [
                'marketing' => $marketingUser,
                'admin' => $adminIds[array_rand($adminIds)],
                'finance' => $financeIds[array_rand($financeIds)],
                'logistik' => $logistikIds[array_rand($logistikIds)],
            ];

            foreach ($roleTasks as $role => $taskInfo) {
                if ($taskInfo['status'] < 0) continue;

                $taskDueDate = match ($role) {
                    'marketing' => $tglRequisisi ?? $createdAt->copy()->addDays(3),
                    'admin' => $tglRilis ?? $createdAt->copy()->addDays(7),
                    'finance' => $tglInvoice ?? $createdAt->copy()->addDays(14),
                    'logistik' => $tglSuratJalan ?? $createdAt->copy()->addDays(21),
                };

                $taskData[] = [
                    'pesanan_id' => $i,
                    'title' => $taskInfo['title'],
                    'role' => $role,
                    'description' => fake()->sentence(4),
                    'due_date' => $taskDueDate,
                    'status' => $taskInfo['status'],
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];

                // Task activities - 1-3 activities per task
                $numActivities = fake()->numberBetween(1, 3);
                for ($a = 0; $a < $numActivities; $a++) {
                    $actUser = $assignedUsers[$role];
                    $actCreatedAt = $createdAt->copy()->addDays($a + 1);

                    $taskActivityData[] = [
                        'created_user_id' => $actUser,
                        'updated_user_id' => $allUserIds[array_rand($allUserIds)],
                        'task_id' => $taskId,
                        'note' => fake()->sentence(4),
                        'pesanan_status' => $taskInfo['status'],
                        'created_at' => $actCreatedAt,
                        'updated_at' => $actCreatedAt,
                    ];
                    $taskActivityId++;
                }

                $taskId++;
            }

            // Log activities - 2-4 log per pesanan
            $numLogs = fake()->numberBetween(2, 4);
            for ($l = 0; $l < $numLogs; $l++) {
                $logActions = ['create', 'update', 'approve', 'print', 'release', 'verify', 'mark_paid'];
                $logData[] = [
                    'user_id' => $allUserIds[array_rand($allUserIds)],
                    'action' => $logActions[array_rand($logActions)],
                    'description' => fake()->sentence(4),
                    'oldData' => null,
                    'newData' => null,
                    'ip_address' => fake()->ipv4(),
                    'user_agent' => fake()->userAgent(),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];
                $logId++;
            }

            $keranjangId++;
        }

        // Batch insert
        DB::table('keranjang')->insert($keranjangData);
        $this->command->info('    -> ' . count($keranjangData) . ' keranjang');

        // Manually set auto-increment for keranjang
        DB::statement("ALTER TABLE keranjang AUTO_INCREMENT = " . ($keranjangId));

        // Insert queue_keranjang in chunks
        foreach (array_chunk($queueKeranjangData, 500) as $chunk) {
            DB::table('queue_keranjang')->insert($chunk);
        }
        $this->command->info('    -> ' . count($queueKeranjangData) . ' queue_keranjang');

        $statusCounts = array_count_values(array_column($pesananData, 'status_pesanan'));
        ksort($statusCounts);
        $statusSummary = implode(', ', array_map(fn($s, $c) => "s{$s}={$c}", array_keys($statusCounts), $statusCounts));
        DB::table('pesanan')->insert($pesananData);
        $this->command->info('    -> ' . count($pesananData) . ' pesanan (' . $statusSummary . ')');

        DB::table('task')->insert($taskData);
        $this->command->info('    -> ' . count($taskData) . ' tasks');

        foreach (array_chunk($taskActivityData, 500) as $chunk) {
            DB::table('task_activity')->insert($chunk);
        }
        $this->command->info('    -> ' . count($taskActivityData) . ' task_activities');

        foreach (array_chunk($logData, 500) as $chunk) {
            DB::table('log_activities')->insert($chunk);
        }
        $this->command->info('    -> ' . count($logData) . ' log_activities');

        // Recalculate auto-increment for tables with foreign keys
        DB::statement("ALTER TABLE queue_keranjang AUTO_INCREMENT = " . ($queueKeranjangId));

        return DB::table('pesanan')->pluck('id')->toArray();
    }

    protected function seedKasHarian(array $userIds, array $companyIds, array $akunIds, array $pesananIds): void
    {
        $entries = [];
        $baseDate = Carbon::parse('2026-01-01');

        for ($i = 1; $i <= 100; $i++) {
            $createdAt = $baseDate->copy()->addDays(fake()->numberBetween(0, 190));
            $debet = fake()->randomFloat(2, 0, 10000000);
            $kredit = fake()->randomFloat(2, 0, 10000000);
            $saldoAwal = fake()->randomFloat(2, 1000000, 50000000);

            $entries[] = [
                'company_internal_id' => $companyIds[array_rand($companyIds)],
                'user_id' => $userIds[array_rand($userIds)],
                'pesanan_id' => $pesananIds[array_rand($pesananIds)],
                'akun_keuangan_id' => $akunIds[array_rand($akunIds)],
                'kategori' => fake()->randomElement([1, 2, 3, 4]),
                'toko' => fake('id_ID')->company(),
                'saldo_awal' => $saldoAwal,
                'debet' => $debet,
                'kredit' => $kredit,
                'saldo_akhir' => $saldoAwal + $debet - $kredit,
                'keterangan' => fake()->sentence(4),
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
        }

        DB::table('kas_harian')->insert($entries);
        $this->command->info('    -> ' . count($entries) . ' kas_harian');
    }
}
