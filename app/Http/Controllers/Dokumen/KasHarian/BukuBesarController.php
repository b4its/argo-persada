<?php

namespace App\Http\Controllers\Dokumen\KasHarian;

use App\Http\Controllers\Controller;
use App\Models\AkunKeuangan;
use App\Models\KasHarian;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BukuBesarController extends Controller
{
    //
public function index(Request $request)
    {
        // === 1. TENTUKAN RENTANG WAKTU FILTER ===
        $filterType = $request->query('filter_type', 'all');
        $startDate = Carbon::create(2000, 1, 1)->startOfDay(); 
        $endDate = Carbon::now()->endOfDay(); 

        switch ($filterType) {
            case 'year':
                if ($request->filled('year')) {
                    $startDate = Carbon::create($request->year, 1, 1)->startOfYear();
                    $endDate = Carbon::create($request->year, 1, 1)->endOfYear();
                }
                break;
            case 'month':
                if ($request->filled('year') && $request->filled('month')) {
                    $startDate = Carbon::create($request->year, $request->month, 1)->startOfMonth();
                    $endDate = Carbon::create($request->year, $request->month, 1)->endOfMonth();
                }
                break;
            case 'week':
                if ($request->filled('date')) {
                    $startDate = Carbon::parse($request->date)->startOfWeek();
                    $endDate = Carbon::parse($request->date)->endOfWeek();
                }
                break;
            case 'day':
                if ($request->filled('date')) {
                    $startDate = Carbon::parse($request->date)->startOfDay();
                    $endDate = Carbon::parse($request->date)->endOfDay();
                }
                break;
            case 'custom':
                if ($request->filled('start_date') && $request->filled('end_date')) {
                    $startDate = Carbon::parse($request->start_date)->startOfDay();
                    $endDate = Carbon::parse($request->end_date)->endOfDay();
                }
                break;
        }

        // === 2. AMBIL DATA MUTASI (DI DALAM RENTANG WAKTU) ===
        $mutasiData = DB::table('kas_harian')
            ->leftJoin('company_internal', 'kas_harian.company_internal_id', '=', 'company_internal.id')
            ->select(
                'kas_harian.akun_keuangan_id', 
                'kas_harian.kategori', 
                'company_internal.name as company_name',
                DB::raw('SUM(kas_harian.debet) as mutasi_debet'),
                DB::raw('SUM(kas_harian.kredit) as mutasi_kredit')
            )
            ->whereBetween('kas_harian.created_at', [$startDate, $endDate])
            ->groupBy('kas_harian.akun_keuangan_id', 'kas_harian.kategori', 'company_internal.name')
            ->get();

        // === 3. AMBIL DATA SALDO AWAL (SEBELUM RENTANG WAKTU) ===
        $awalData = DB::table('kas_harian')
            ->leftJoin('company_internal', 'kas_harian.company_internal_id', '=', 'company_internal.id')
            ->select(
                'kas_harian.akun_keuangan_id', 
                'kas_harian.kategori', 
                'company_internal.name as company_name',
                DB::raw('SUM(kas_harian.debet) as awal_debet'),
                DB::raw('SUM(kas_harian.kredit) as awal_kredit')
            )
            ->where('kas_harian.created_at', '<', $startDate) 
            ->groupBy('kas_harian.akun_keuangan_id', 'kas_harian.kategori', 'company_internal.name')
            ->get();

        // === 4. SATUKAN DATA MENGGUNAKAN KEY GABUNGAN ===
        $rawData = [];

        foreach ($awalData as $row) {
            $key = $row->kategori . '_' . $row->akun_keuangan_id . '_' . $row->company_name;
            $rawData[$key] = [
                'kategori_id'  => $row->kategori,
                'akun_id'      => $row->akun_keuangan_id,
                'company_name' => $row->company_name ?? '',
                'saldo_awal'   => $row->awal_debet - $row->awal_kredit, 
                'mutasi_debet' => 0,
                'mutasi_kredit'=> 0,
            ];
        }

        foreach ($mutasiData as $row) {
            $key = $row->kategori . '_' . $row->akun_keuangan_id . '_' . $row->company_name;
            if (!isset($rawData[$key])) {
                $rawData[$key] = [
                    'kategori_id'  => $row->kategori,
                    'akun_id'      => $row->akun_keuangan_id,
                    'company_name' => $row->company_name ?? '',
                    'saldo_awal'   => 0,
                    'mutasi_debet' => $row->mutasi_debet,
                    'mutasi_kredit'=> $row->mutasi_kredit,
                ];
            } else {
                $rawData[$key]['mutasi_debet'] = $row->mutasi_debet;
                $rawData[$key]['mutasi_kredit']= $row->mutasi_kredit;
            }
        }

        // === 5. GROUPING BERDASARKAN KATEGORI & KALKULASI TOTAL ===
        $kategoriNames = [
            1 => 'PENJUALAN',
            2 => 'PIUTANG',
            3 => 'BIAYA UMUM DAN ADMINISTRASI KANTOR',
            4 => 'BIAYA LAIN-LAIN',
        ];

        $akunIds = collect($rawData)->pluck('akun_id')->unique()->filter()->toArray();
        $akuns = AkunKeuangan::whereIn('id', $akunIds)->get()->keyBy('id');

        $groupedData = [];

        $totalAwal = 0;
        $totalDebet = 0;
        $totalKredit = 0;

        foreach ($rawData as $row) {
            $akun = $akuns->get($row['akun_id']);
            if (!$akun) continue;

            $totalAwal   += $row['saldo_awal']; 
            $totalDebet  += $row['mutasi_debet'];
            $totalKredit += $row['mutasi_kredit'];

            $netAwal  = $row['saldo_awal'];
            $mutasiD  = $row['mutasi_debet'];
            $mutasiK  = $row['mutasi_kredit'];
            $netAkhir = $netAwal + $mutasiD - $mutasiK;

            $kategoriName = $kategoriNames[$row['kategori_id']] ?? 'KATEGORI LAINNYA (' . $row['kategori_id'] . ')';

            if (!isset($groupedData[$kategoriName])) {
                $groupedData[$kategoriName] = [];
            }

            $groupedData[$kategoriName][] = [
                'company_name'  => $row['company_name'], 
                'kode_akun'     => $akun->kode,
                'nama_akun'     => $akun->name,
                'awal_debet'    => $netAwal > 0 ? $netAwal : 0,
                'awal_kredit'   => $netAwal < 0 ? abs($netAwal) : 0,
                'mutasi_debet'  => $mutasiD,
                'mutasi_kredit' => $mutasiK,
                'akhir_debet'   => $netAkhir > 0 ? $netAkhir : 0,
                'akhir_kredit'  => $netAkhir < 0 ? abs($netAkhir) : 0,
            ];
        }

        // === 6. FINALISASI GRAND TOTAL (FULL NETTO) ===
        $totalAkhirNetto = $totalAwal + $totalDebet - $totalKredit;
        
        // Cari selisih mutasi agar tidak berdampingan di baris JUMLAH
        $netMutasi = $totalDebet - $totalKredit;

        $grandTotals = [
            'awal_debet'    => $totalAwal > 0 ? $totalAwal : 0,
            'awal_kredit'   => $totalAwal < 0 ? abs($totalAwal) : 0,
            
            // Mutasi sekarang diselisihkan (Netto), dipastikan hanya isi satu sisi
            'mutasi_debet'  => $netMutasi > 0 ? $netMutasi : 0,
            'mutasi_kredit' => $netMutasi < 0 ? abs($netMutasi) : 0,
            
            'akhir_debet'   => $totalAkhirNetto > 0 ? $totalAkhirNetto : 0,
            'akhir_kredit'  => $totalAkhirNetto < 0 ? abs($totalAkhirNetto) : 0,
        ];

        return view('dokumen.finance.buku-besar', [
            'groupedData' => $groupedData,
            'grandTotals' => $grandTotals
        ]);
    }
}
