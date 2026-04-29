<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class InvoiceFinanceExport implements FromView, ShouldAutoSize
{
    protected $pesanan;

    public function __construct($pesanan)
    {
        $this->pesanan = $pesanan;
    }

    public function view(): View
    {
        return view('dokumen.excel_surat_invoice_finance', [
            'pesanan' => $this->pesanan
        ]);
    }
}