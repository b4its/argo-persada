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
        $this->applyPeriodeFilter($query, $request);

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

        $this->applyPeriodeFilter($query, $request);

        $pesananAll = $query->get();

        // Download file Excel menggunakan class Export
        return Excel::download(new SuratPesananExport($pesananAll), 'Monitoring_PO_Masuk_2026.xlsx');
    }

    private function applyPeriodeFilter($query, Request $request): void
    {
        if (!$request->has('periode')) {
            return;
        }

        $dateExpr = \Illuminate\Support\Facades\DB::raw('COALESCE(tanggal_po, date(created_at))');

        switch ($request->periode) {
            case 'minggu':
                $query->whereBetween($dateExpr, [
                    now()->startOfWeek()->toDateString(),
                    now()->endOfWeek()->toDateString(),
                ]);
                break;

            case 'bulan':
                $query->whereBetween($dateExpr, [
                    now()->startOfMonth()->toDateString(),
                    now()->endOfMonth()->toDateString(),
                ]);
                break;

            case 'tahun':
                $query->whereBetween($dateExpr, [
                    now()->startOfYear()->toDateString(),
                    now()->endOfYear()->toDateString(),
                ]);
                break;

            case 'custom':
                if ($request->start_date && $request->end_date) {
                    $query->whereBetween($dateExpr, [
                        Carbon::parse($request->start_date)->toDateString(),
                        Carbon::parse($request->end_date)->toDateString(),
                    ]);
                }
                break;
        }
    }
}