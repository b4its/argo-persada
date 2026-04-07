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
        Schema::create('mutasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_buku_Besar')->nullable()->constrained('buku_besar')->onDelete('cascade');
            $table->string('code')->nullable()->nullable();
            $table->string('name')->nullable();
            $table->decimal('saldo_awal', 25, 2)->default(0); 
            $table->decimal('saldo_akhir', 25, 2)->default(0); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mutasi');
    }
};
