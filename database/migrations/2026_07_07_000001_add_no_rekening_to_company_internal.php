<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_internal', function (Blueprint $table) {
            $table->string('no_rekening')->nullable()->after('gambar');
            $table->string('nama_bank')->nullable()->after('no_rekening');
            $table->string('cabang_bank')->nullable()->after('nama_bank');
        });
    }

    public function down(): void
    {
        Schema::table('company_internal', function (Blueprint $table) {
            $table->dropColumn(['no_rekening', 'nama_bank', 'cabang_bank']);
        });
    }
};
