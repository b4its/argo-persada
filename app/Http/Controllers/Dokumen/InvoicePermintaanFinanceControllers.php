<?php

namespace App\Http\Controllers\Dokumen;

use App\Exports\InvoiceFinanceExport; // <-- Tambahkan ini
use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel; // <-- Tambahkan ini

class InvoicePermintaanFinanceControllers extends Controller
{
    public function index($id)
    {
        $latestPesanan = Pesanan::with(['keranjang'])->findOrFail($id);
        $username = Auth::user()->username; // Atau Auth::user()->name tergantung struktur DB Anda
        return view("dokumen.surat_invoice_finance", [
            "latestPesanan" => $latestPesanan,
            "username" => $username
        ]);
    }

    public function exportExcel($id)
    {
        // Menarik data Pesanan beserta relasi item keranjangnya
        $pesanan = Pesanan::with(['keranjang.queueKeranjang'])->findOrFail($id);
        
        // Penamaan file dinamis berdasarkan Nomor Invoice / ID
        $namaFile = 'Invoice_Finance_' . ($pesanan->no_invoice ?? $pesanan->id) . '.xlsx';

        return Excel::download(new InvoiceFinanceExport($pesanan), $namaFile);
    }
}