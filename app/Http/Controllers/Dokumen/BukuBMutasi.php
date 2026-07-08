<?php

namespace App\Http\Controllers\Dokumen;

use App\Http\Controllers\Controller;
use App\Models\BukuBesar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BukuBMutasi extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        // Mencari Buku Besar berdasarkan ID.
        // findOrFail akan otomatis menampilkan halaman 404 jika ID tidak ditemukan.
        // Tetap gunakan eager loading 'with' untuk performa.
        $bukuBesar = BukuBesar::with(['mutasis.mutasiItems'])->findOrFail($id);
        
        $username = Auth::user()->name;
        
        return view('dokumen.finance.buku_besar_mutasi', [
            "bukuBesar" => $bukuBesar, // Variabel diubah menjadi lebih relevan
            "username" => $username
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
