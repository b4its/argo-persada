<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

// Pastikan TIDAK ADA "FromCollection" di sini
class BukuBesarExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $groupedData;
    protected $grandTotals;

    public function __construct($groupedData, $grandTotals)
    {
        $this->groupedData = $groupedData;
        $this->grandTotals = $grandTotals;
    }

    public function view(): View
    {
        return view('dokumen.finance.excel_buku_besar', [
            'groupedData' => $this->groupedData,
            'grandTotals' => $this->grandTotals
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Baris 1, 2, dan 3 adalah Header Tabel, kita buat Bold otomatis
            1 => ['font' => ['bold' => true]],
            2 => ['font' => ['bold' => true]],
            3 => ['font' => ['bold' => true]],
        ];
    }
}