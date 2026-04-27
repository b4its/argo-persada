<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SuratPesananExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $pesananAll;

    public function __construct($pesananAll)
    {
        $this->pesananAll = $pesananAll;
    }

    public function view(): View
    {
        $dataFromDB = [];
        
        foreach($this->pesananAll as $pesanan) {
            $items = DB::table('queue_keranjang')
                        ->where('keranjang_id', $pesanan->keranjang_id)
                        ->get();

            if($items->count() > 0) {
                foreach($items as $item) {
                    $dataFromDB[] = $this->formatRow($pesanan, $item);
                }
            } else {
                // Fallback jika kosong
                $dataFromDB[] = $this->formatRow($pesanan, null);
            }
        }

        return view('dokumen.excel_surat_pesanan', [
            'dataFromDB' => $dataFromDB
        ]);
    }

    private function formatRow($pesanan, $item)
    {
        return [
            'tgl_po'      => $pesanan->created_at ? Carbon::parse($pesanan->created_at)->format('d/m/y') : '-',
            'group'       => $pesanan->group_name ?? '-',
            'company'     => $pesanan->company_name ?? '-',
            'no_po'       => $pesanan->code ?? '-',
            'nama_barang' => $item->item_name ?? '-',
            'qty'         => $item->quantity ?? 0,
            'stn'         => $item->satuan ?? '-',
            'h_po'        => $item->po ?? 0,
            'h_modal'     => $item->modal ?? 0,
            'suplier'     => $item->supplier_name ?? '-',
            'no_db'       => $pesanan->no_po ?? '-',
            'tgl_rilis'   => $pesanan->tanggal_rilis_dana ? Carbon::parse($pesanan->tanggal_rilis_dana)->format('d/m/y') : '-',
            'no_do'       => $pesanan->no_delivery_order ?? '-',
            'tgl_do'      => $pesanan->tanggal_terbit_surat_jalan ? Carbon::parse($pesanan->tanggal_terbit_surat_jalan)->format('d/m/y') : '-',
            'tgl_kembali' => $pesanan->tanggal_surat_kembali ? Carbon::parse($pesanan->tanggal_surat_kembali)->format('d/m/y') : '-',
            'tgl_inv'     => $pesanan->tanggal_terbit_invoice ? Carbon::parse($pesanan->tanggal_terbit_invoice)->format('d/m/y') : '-',
            'no_inv'      => $pesanan->no_invoice ?? '-',
            'tgl_lunas'   => $pesanan->tanggal_lunas ? Carbon::parse($pesanan->tanggal_lunas)->format('d/m/y') : '-',
            'ket'         => $item->keterangan ?? '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Menambahkan border pada seluruh cell secara otomatis
        return [
            // Baris 1 dan 2 adalah Header
            1 => ['font' => ['bold' => true]],
            2 => ['font' => ['bold' => true]],
        ];
    }
}