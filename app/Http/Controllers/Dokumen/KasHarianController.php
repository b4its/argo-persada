<?php

namespace App\Http\Controllers\Dokumen;

use App\Http\Controllers\Controller;
use App\Models\KasHarian;
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

    public function index_all()
    {
        // Menggunakan eager loading untuk menarik semua relasi terkait dalam satu query
        $kasHarian = KasHarian::with([
            'saldo', 
            'companyInternal', 
            'user', 
            'pesanan'
        ])->get();

        $username = Auth::user()->name;

        return view("dokumen.finance.kas_harian_all", [
            'kasHarian' => $kasHarian, 
            'username'  => $username
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
