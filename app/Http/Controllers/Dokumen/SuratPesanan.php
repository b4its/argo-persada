<?php

namespace App\Http\Controllers\Dokumen;

use App\Exports\SuratPesananExport;
use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class SuratPesanan extends Controller
{
    //


    public function index(Request $request) 
    {
        $username = Auth::user()->name;
        $query = Pesanan::query();

        // Filter berdasarkan periode yang dikirim dari Filament
        if ($request->has('periode')) {
            switch ($request->periode) {
                case 'minggu':
                    $query->whereBetween('created_at', [
                        now()->startOfWeek(), 
                        now()->endOfWeek()
                    ]);
                    break;

                case 'bulan':
                    $query->whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year);
                    break;

                case 'tahun':
                    $query->whereYear('created_at', now()->year);
                    break;

                case 'custom':
                    if ($request->start_date && $request->end_date) {
                        $query->whereBetween('created_at', [
                            Carbon::parse($request->start_date)->startOfDay(),
                            Carbon::parse($request->end_date)->endOfDay(),
                        ]);
                    }
                    break;
            }
        }

        $pesananAll = $query->get();

        return view('dokumen.surat_pesanan', [
            "pesananAll" => $pesananAll,
            "username" => $username,
            "periode" => $request->periode // Opsional: untuk menampilkan keterangan di blade
        ]);
    }

    public function exportExcel(Request $request)
    {
        $query = Pesanan::query();

        // Filter yang sama persis dengan index
        if ($request->has('periode')) {
            switch ($request->periode) {
                case 'minggu':
                    $query->whereBetween('created_at', [
                        now()->startOfWeek(), 
                        now()->endOfWeek()
                    ]);
                    break;
                case 'bulan':
                    $query->whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year);
                    break;
                case 'tahun':
                    $query->whereYear('created_at', now()->year);
                    break;
                case 'custom':
                    if ($request->start_date && $request->end_date) {
                        $query->whereBetween('created_at', [
                            Carbon::parse($request->start_date)->startOfDay(),
                            Carbon::parse($request->end_date)->endOfDay(),
                        ]);
                    }
                    break;
            }
        }

        $pesananAll = $query->get();

        // Download file Excel menggunakan class Export
        return Excel::download(new SuratPesananExport($pesananAll), 'Monitoring_PO_Masuk_2026.xlsx');
    }

}