<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KasHarianExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $kasHarian;

    public function __construct($kasHarian)
    {
        $this->kasHarian = $kasHarian;
    }

    public function view(): View
    {
        return view('dokumen.finance.excel_kas_harian_all', [
            'kasHarian' => $this->kasHarian
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        // Memastikan baris pertama (Header) di-bold
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}