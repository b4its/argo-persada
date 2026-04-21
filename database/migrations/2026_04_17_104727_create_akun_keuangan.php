<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('akun_keuangan', function (Blueprint $table) {
            $table->id();
            $table->string("kode")->nullable()->unique();
            $table->string("name")->nullable();
            $table->tinyInteger("kategori")->nullable()->comment('1. Penjualan, 2. Piutang, 3. Biaya Umum dan Administrasi Kantor, 4. Biaya Lain lain');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('akun_keuangan');
    }
};
