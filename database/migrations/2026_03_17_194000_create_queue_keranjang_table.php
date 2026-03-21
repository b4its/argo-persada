<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queue_keranjang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('keranjang_id')->constrained('keranjang')->onDelete('cascade'); 
            $table->string('supplier_name')->nullable();
            $table->string('keterangan')->nullable();
            $table->string('item_name')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('satuan')->nullable();
            $table->decimal('modal', 25, 2)->nullable();
            $table->decimal('po', 25, 2)->nullable();
            $table->decimal('sub_total', 25, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queue_keranjang');
    }
};