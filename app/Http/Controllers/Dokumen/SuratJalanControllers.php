<?php

namespace App\Http\Controllers\Dokumen;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\QueueKeranjang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuratJalanControllers extends Controller
{
    //
    public function index($id, Request $request){
        $latestPesanan = Pesanan::with(['keranjang.queueKeranjang', 'companyInternal'])->findOrFail($id);
        $username = Auth::user()->name;
        
        // Revisi 5: Pilih item spesifik yang akan dikirim via query parameter item_ids (dipisah koma)
        $selectedItemIds = $request->query('item_ids');
        $selectedItems = collect();
        if ($selectedItemIds) {
            $ids = explode(',', $selectedItemIds);
            $selectedItems = QueueKeranjang::whereIn('id', $ids)
                ->where('keranjang_id', $latestPesanan->keranjang_id)
                ->get();
        }
        
        // Revisi 8: Custom nama pengirim dari query parameter
        $customSenderName = $request->query('sender_name', $username);
        
        return view("dokumen.surat_jalan", 
        [
            "latestPesanan"=> $latestPesanan,
            "username"=> $username,
            "selectedItems" => $selectedItems,
            "selectedItemIds" => $selectedItemIds,
            "customSenderName" => $customSenderName,
            "request" => $request,
        ]);
    }


    
}
