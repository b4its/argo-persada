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
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('keranjang_id')->constrained('keranjang')->onDelete('cascade');
            
            // Identitas Pesanan 
            $table->string('code', 45)->unique(); // No. Pemesanan / PO
            $table->decimal('ppn', 15, 2)->default(0);
            $table->decimal('total_harga', 15, 2)->default(0);
            
            // Data Perusahaan 
            $table->string('group_name')->nullable();
            $table->string('company_name')->nullable();
            $table->text('address')->nullable();

            // Nomor Dokumen per Divisi 
            $table->string('no_requisition')->nullable(); // Marketing
            $table->string('no_invoice')->nullable();     // Finance
            $table->string('no_delivery_order')->nullable(); // Logistik

            // Tanggal-Tanggal Penting 
            $table->date('tanggal_rilis_dana')->nullable(); 
            $table->date('tanggal_terbit_invoice')->nullable();
            $table->date('tanggal_jatuh_tempo')->nullable();
            $table->date('tanggal_lunas')->nullable();

            // File - Download Surat)
            $table->string('file_invoice')->nullable();
            $table->string('file_do')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
