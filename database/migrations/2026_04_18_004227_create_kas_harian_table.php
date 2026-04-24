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
        Schema::create('kas_harian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_internal_id')->nullable()->constrained('company_internal')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('akun_keuangan_id')->nullable()->constrained('akun_keuangan')->onDelete('cascade');
            $table->foreignId('pesanan_id')->nullable()->constrained('pesanan')->onDelete('cascade');
            $table->tinyInteger('metode_pembayaran')->default(0)->comment("0. belum ditentukan, 1. Tunai, 2. Kredit"); 
            $table->string('toko')->nullable();
            $table->decimal('saldo_awal', 25, 2)->default(0); 
            $table->decimal('debet', 25, 2)->default(0); 
            $table->decimal('kredit', 25, 2)->default(0); 
            $table->decimal('saldo_akhir', 25, 2)->default(0); 
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kas_harian');
    }
};
