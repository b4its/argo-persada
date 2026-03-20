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
        Schema::create('task', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->nullable()->constrained('pesanan')->onDelete('cascade');
            $table->string('title');
            $table->string('role')->nullable();
            $table->text('description')->nullable();
            $table->dateTime('due_date')->nullable()->comment('marketing: requisition, 2: finance: invoice, 3: logistik: delivery order');
            $table->tinyInteger('status')->default(0)->comment('0: pending, 1: in progress, 2: completed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task');
    }
};
