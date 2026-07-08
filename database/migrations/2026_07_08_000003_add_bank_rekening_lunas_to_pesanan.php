<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->string('nama_bank_lunas')->nullable()->after('no_rekening_rilis_dana');
            $table->string('no_rekening_lunas')->nullable()->after('nama_bank_lunas');
        });
    }

    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn(['nama_bank_lunas', 'no_rekening_lunas']);
        });
    }
};
