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
        Schema::create('queue_keranjang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('supplier_name')->nullable();
            $table->string('keterangan')->nullable();
            $table->string('item_name')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('satuan')->nullable();
            $table->decimal('modal', 10, 2)->nullable();
            $table->decimal('po', 10, 2)->nullable();
            $table->decimal('sub_total', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queue_keranjang');
    }
};
