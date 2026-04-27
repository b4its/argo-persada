<?php

namespace App\Http\Controllers\Dokumen;

use App\Http\Controllers\Controller;
use App\Models\KasHarian;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KasHarianController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    
    public function index($id)
    {
        // Menggunakan eager loading untuk menarik semua relasi terkait dalam satu query
        $kasHarian = KasHarian::with([
            'saldo', 
            'companyInternal', 
            'user', 
            'pesanan'
        ])->findOrFail($id);

        $username = Auth::user()->name;

        return view("dokumen.finance.kas_harian", [
            'kasHarian' => $kasHarian, // Saya ubah variabelnya menjadi lebih umum (tidak harus 'latest')
            'username'  => $username
        ]);
    }

        public function index_all(Request $request)
        {
            $query = KasHarian::with([
                'akunKeuangan', 
                'companyInternal', 
                'user', 
                'pesanan'
            ]);

            // === LOGIKA FILTERING WAKTU ===
            $filterType = $request->query('filter_type', 'all');

            switch ($filterType) {
                case 'year':
                    if ($request->filled('year')) {
                        $query->whereYear('created_at', $request->year);
                    }
                    break;
                case 'month':
                    if ($request->filled('year')) {
                        $query->whereYear('created_at', $request->year);
                    }
                    if ($request->filled('month')) {
                        $query->whereMonth('created_at', $request->month);
                    }
                    break;
                case 'week':
                    if ($request->filled('date')) {
                        $start = Carbon::parse($request->date)->startOfWeek();
                        $end = Carbon::parse($request->date)->endOfWeek();
                        $query->whereBetween('created_at', [$start, $end]);
                    }
                    break;
                case 'day':
                    if ($request->filled('date')) {
                        $query->whereDate('created_at', $request->date);
                    }
                    break;
                case 'custom':
                    if ($request->filled('start_date') && $request->filled('end_date')) {
                        $query->whereBetween('created_at', [
                            Carbon::parse($request->start_date)->startOfDay(), 
                            Carbon::parse($request->end_date)->endOfDay()
                        ]);
                    }
                    break;
            }

            $kasHarian = $query->orderBy('created_at', 'asc')->get();
            $username = Auth::user()->name;

            return view("dokumen.finance.kas_harian_all", [
                'kasHarian' => $kasHarian, 
                'username'  => $username,
                'filterType' => $filterType // Dilempar ke blade jika ingin print info "Periode Laporan"
            ]);
        }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
