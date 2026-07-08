<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('akun_keuangan', function (Blueprint $table) {
            $table->tinyInteger('kategori')->nullable()->after('kode')->comment('1. Penjualan, 2. Piutang, 3. Biaya Umum, 4. Biaya Lain');
        });
    }

    public function down(): void
    {
        Schema::table('akun_keuangan', function (Blueprint $table) {
            $table->dropColumn('kategori');
        });
    }
};
