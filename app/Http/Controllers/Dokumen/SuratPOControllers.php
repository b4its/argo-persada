<?php

namespace App\Http\Controllers\Dokumen;

use App\Exports\SuratPOExport; // <-- Tambahkan ini
use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel; // <-- Tambahkan ini

class SuratPOControllers extends Controller
{
    public function index($id)
    {
        $latestPesanan = Pesanan::with(['keranjang'])->findOrFail($id);
        $username = Auth::user()->username;
        return view("dokumen.surat_po", [
            "latestPesanan" => $latestPesanan,
            "username" => $username
        ]);
    }

    public function exportExcel($id)
    {
        // Ambil data spesifik berdasarkan ID
        $pesanan = Pesanan::with(['keranjang.queueKeranjang'])->findOrFail($id);
        
        $namaFile = 'Daftar_Belanja_PO_' . ($pesanan->no_po ?? $pesanan->id) . '.xlsx';

        // Download file Excel
        return Excel::download(new SuratPOExport($pesanan), $namaFile);
    }
}