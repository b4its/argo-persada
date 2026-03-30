<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Belanja - Invoice Surat PO</title>
    <style>
        /* Pengaturan Gaya Dasar */
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            font-size: 13px;
            color: #333;
        }
        
        /* Tombol Cetak */
        .btn-print {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .btn-print:hover {
            background-color: #0056b3;
        }

        /* Bagian Header (Informasi PO) */
        .header-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 20px;
        }
        .info-table {
            width: auto;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .info-table td {
            padding: 3px 10px 3px 0;
            vertical-align: top;
        }

        /* Tabel Data Utama */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 8px 6px;
            text-align: center;
        }
        .data-table th {
            background-color: #f2f2f2;
            text-transform: uppercase;
            font-weight: bold;
        }
        a {
            text-decoration: none;
        }
        
        /* Penyesuaian Perataan Kolom Khusus */
        .data-table td:nth-child(4) {
            text-align: left; /* Kolom Deskripsi */
        }
        .data-table td:nth-child(7),
        .data-table td:nth-child(8),
        .data-table td:nth-child(9),
        .data-table tfoot th:nth-child(2) {
            text-align: right; /* Kolom Harga dan Total */
        }

        /* Pengaturan Khusus Saat Mode Print (PDF) */
        @media print {
            /* Aturan @page margin: 0 menghilangkan URL, Tanggal, dan Judul bawaan browser */
            @page {
                size: A4 landscape;
                margin: 0; 
            }
            body {
                /* Memberikan jarak konten dari tepi kertas agar tidak terpotong */
                margin: 15mm; 
                padding: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .btn-print {
                display: none; /* Sembunyikan tombol saat diubah ke PDF */
            }
        }
    </style>
</head>
<body>

    @php
        // Mengambil item pertama dari Collection yang dikirimkan oleh controller
        $pesanan = $latestPesanan->first();
    @endphp

    <button class="btn-print" onclick="window.print()">🖨️ Cetak ke PDF</button> <br>
    <a href="{{ route("filament.marketing.resources.pesanan.index") }}" class="btn-print">Kembali</a>

    <div class="header-title">
        DAFTAR BELANJA
    </div>

    @if($pesanan)
    <table class="info-table">
        <tr>
            <td>PURCHASE INVOICE NUMBER</td>
            <td>:</td>
            <td>{{ $pesanan->no_invoice ?? '-' }}</td>
        </tr>
        <tr>
            <td>Tanggal Supply</td>
            <td>:</td>
            <td>{{ $pesanan->created_at ? $pesanan->created_at->format('d-m-Y') : date('d-m-Y') }}</td>
        </tr>
        <tr>
            <td>DEPARTMENT</td>
            <td>:</td>
            <td>{{ $pesanan->group_name ?? 'SUPPLY' }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th>NO</th>
                <th>PT</th>
                <th>NO INVOICE</th>
                <th>Deskripsi</th>
                <th>Qty</th>
                <th>Satuan</th>
                <th>PO</th>
                <th>MODAL</th>
                <th>TOTAL</th>
                <th>Supplier</th>
                <th>KET</th>
            </tr>
        </thead>
        <tbody>
            @if($pesanan->keranjang && $pesanan->keranjang->queueKeranjang)
                @foreach($pesanan->keranjang->queueKeranjang as $item)
                <tr>
                    <td>{{ $loop->first ? 1 : '' }}</td>
                    <td>{{ $loop->first ? $pesanan->company_name : '' }}</td>
                    <td>{{ $loop->first ? $pesanan->no_po : '' }}</td>
                    
                    <td>{{ $item->item_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->satuan }}</td>
                    <td>{{ number_format($item->po, 0, ',', '.') }}</td>
                    <td>{{ number_format($item->modal, 0, ',', '.') }}</td>
                    <td>{{ number_format($item->sub_total, 0, ',', '.') }}</td>
                    <td>{{ $item->supplier_name }}</td>
                    <td>{{ $item->keterangan }}</td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="11">Data rincian belanja tidak ditemukan.</td>
                </tr>
            @endif
        </tbody>
        <tfoot>
            <tr>
                <th colspan="8" style="text-align: right;">TOTAL KESELURUHAN</th>
                <th>{{ number_format($pesanan->total_harga, 0, ',', '.') }}</th>
                <th colspan="2"></th>
            </tr>
        </tfoot>
    </table>
    @else
        <p style="text-align:center; font-weight:bold;">Data Pesanan Tidak Ditemukan</p>
    @endif


    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>