<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SuratPOExport implements FromView, ShouldAutoSize
{
    protected $pesanan;

    // Karena ini untuk 1 ID Surat PO, kita hanya melempar 1 object Pesanan
    public function __construct($pesanan)
    {
        $this->pesanan = $pesanan;
    }

    public function view(): View
    {
        return view('dokumen.excel_surat_po', [
            'pesanan' => $this->pesanan
        ]);
    }
}