<?php

use App\Models\Pesanan;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Pesanan::fixExistingRequisitionNumbers();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed for random alphanumeric suffix migration
    }
};
