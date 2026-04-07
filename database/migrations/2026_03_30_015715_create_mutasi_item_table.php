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
        Schema::create('mutasi_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_mutasi')->nullable()->constrained('mutasi')->onDelete('cascade');
            $table->string('no_ref')->nullable();
            $table->text('keterangan')->nullable();
            $table->decimal('debet', 25, 2)->default(0); 
            $table->decimal('kredit', 25, 2)->default(0);
            $table->decimal('saldo', 25, 2)->default(0); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mutasi_item');
    }
};
