<?php

namespace App\Http\Controllers\Dokumen;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuratJalanControllers extends Controller
{
    //
    public function index($id){
        $latestPesanan = Pesanan::with(['keranjang'])->findOrFail($id);
        $username = Auth::user()->name;
        return view("dokumen.surat_jalan", 
        [
            "latestPesanan"=> $latestPesanan,
            "username"=> $username
        
        ]);
    }


    
}
