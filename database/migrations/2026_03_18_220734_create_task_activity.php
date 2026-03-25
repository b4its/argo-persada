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
        Schema::create('task_activity', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('task_id')->constrained('task')->onDelete('cascade');
            $table->text('note')->nullable(); 
            $table->tinyInteger('pesanan_status')->default(0)->comment('0: dibuat, 1: pending, 2: perlu rilis dana, 3: perlu cetak invoice, 4: perlu penagihan, 5: ditandai lunas, 6. cetak surat jalan, 7. tandai selesai dikirm, 8: selesai');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_activity');
    }
};
