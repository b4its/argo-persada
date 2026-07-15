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
        Schema::table('company_internal', function (Blueprint $table) {
            $table->string('nama_pemilik_bank')->nullable()->after('nama_bank');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_internal', function (Blueprint $table) {
            $table->dropColumn('nama_pemilik_bank');
        });
    }
};
