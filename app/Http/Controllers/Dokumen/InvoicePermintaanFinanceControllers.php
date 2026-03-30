<?php

namespace App\Http\Controllers\Dokumen;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoicePermintaanFinanceControllers extends Controller
{
    //
    public function index(){
        $latestPesanan = Pesanan::latest()->limit(1)->get();
        $username = Auth::user()->username;
        return view("dokumen.surat_jalan", 
        [
            "latestPesanan"=> $latestPesanan,
            "username"=> $username
        
        ]);
    }
}
