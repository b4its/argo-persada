<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan - Pengiriman Barang</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 30px;
            font-size: 11pt;
            color: #000;
            background: #fff;
        }

        /* Tombol aksi */
        .action-buttons {
            margin-bottom: 20px;
        }
        .btn-print {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
            margin-right: 10px;
        }
        .btn-print:hover { background-color: #0056b3; }
        .btn-back { background-color: #6c757d; }
        .btn-back:hover { background-color: #5a6268; }

        /* Wrapper dokumen — lebar A4 */
        .document-wrapper {
            width: 100%;
            max-width: 740px;
            margin: 0 auto;
        }

        /* ── HEADER ────────────────────────────────────────────── */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
            line-height: 1.6;
            font-size: 11pt;
        }
        .header-table td {
            padding: 1px 2px;
            vertical-align: top;
        }
        .col-company  { width: 42%; font-weight: bold; font-size: 12pt; }
        .col-label    { width: 13%; }
        .col-colon    { width: 3%;  }
        .col-value    { width: 42%; }

        /* ── JUDUL SURAT JALAN ──────────────────────────────────── */
        .title-surat-jalan {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin: 10px 0 6px 0;
            letter-spacing: 1px;
        }

        /* ── NOMOR DO & PO ──────────────────────────────────────── */
        .no-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
            font-weight: bold;
            font-size: 11pt;
        }
        .no-table td { padding: 2px 0; }
        .col-no { width: 55%; }
        .col-po { width: 45%; }

        /* ── TABEL BARANG ───────────────────────────────────────── */
        /*
            Kolom Excel (disesuaikan dengan proporsi):
            A  = No       (narrow)
            B–E= Nama Barang (merged, lebar)
            F  = Jumlah
            G  = Satuan
        */
        .table-items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
            font-size: 11pt;
        }
        .table-items th,
        .table-items td {
            padding: 3px 6px;
            line-height: 1.5;
        }
        /* Header row — full border */
        .table-items thead th {
            border: 1px solid #000;
            text-align: center;
            font-weight: bold;
            background: #fff;
        }
        /* Data rows — hanya border kiri, kanan, dan outer bawah pada kolom yg diberi border di Excel */
        /* Kolom No (A) */
        .table-items tbody td.col-no {
            border-left: 1px solid #000;
            border-right: none;
            text-align: center;
            width: 5%;
        }
        /* Kolom Nama Barang (B-E) */
        .table-items tbody td.col-nama {
            border-left: 1px solid #000;
            border-right: none;
            text-align: left;
            width: 55%;
        }
        /* Kolom Jumlah (F) */
        .table-items tbody td.col-jumlah {
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            text-align: center;
            width: 20%;
        }
        /* Kolom Satuan (G) */
        .table-items tbody td.col-satuan {
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            text-align: center;
            width: 20%;
        }
        /* Baris kosong harus tetap punya tinggi */
        .table-items tbody tr { height: 21px; }

        /* Baris JUMLAH (row 32 di Excel) — border atas & bawah penuh, mirip Excel */
        .table-items tfoot tr.row-jumlah td {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }
        .table-items tfoot tr.row-jumlah td.col-label-jumlah {
            border-left: 1px solid #000;
            border-right: none;
            text-align: right;
            padding-right: 6px;
        }
        .table-items tfoot tr.row-jumlah td.col-total-qty {
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            text-align: center;
        }
        .table-items tfoot tr.row-jumlah td.col-total-satuan {
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            text-align: center;
        }

        /* ── TANDA TANGAN ───────────────────────────────────────── */
        /*
            Excel: A33:B33 = Penerima, C33:E33 = Pengirim, F33:G33 = Hormat Kami
            Masing2 punya border kiri, kanan, atas; border bawah tidak ada di header
        */
        .ttd-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
            font-size: 11pt;
        }
        .ttd-table td { padding: 2px 6px; vertical-align: top; }

        .ttd-header td {
            border-top: 1px solid #000;
            text-align: center;
            font-weight: normal;
            padding: 3px 6px;
        }
        .ttd-header td.cell-penerima {
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            width: 33.33%;
        }
        .ttd-header td.cell-pengirim {
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            width: 33.33%;
        }
        .ttd-header td.cell-hormat {
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            width: 33.33%;
        }

        /* Baris ruang tanda tangan (tinggi ~60px seperti Excel rows 34-37) */
        .ttd-space td {
            height: 55px;
            border-left: 1px solid #000;
            border-right: 1px solid #000;
        }
        .ttd-space td.cell-end { border-right: 1px solid #000; }

        /* Baris nama & tanggal */
        .ttd-info td {
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 2px 8px;
            line-height: 1.7;
        }

        /* ── CATATAN / NOTE ─────────────────────────────────────── */
        .note-section {
            margin-top: 12px;
            font-size: 10.5pt;
            line-height: 1.6;
        }
        .note-table {
            border-collapse: collapse;
            line-height: 1.6;
        }
        .note-table td { padding: 1px 4px; vertical-align: top; }
        .note-label  { width: 60px; }
        .note-colon  { width: 14px; }
        .note-lembar { width: 100px; }
        .note-colon2 { width: 14px; }

        .disclaimer {
            margin-top: 10px;
            font-size: 10.5pt;
            line-height: 1.7;
        }

        /* ── PRINT ──────────────────────────────────────────────── */
        @media print {
            @page {
                size: A4 portrait;
                margin: 12mm 15mm 12mm 15mm;
            }
            body {
                padding: 2em;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .action-buttons { display: none; }
            .document-wrapper { max-width: 100%; }
        }
    </style>
</head>
<body>

@php
    $pesanan = isset($latestPesanan->id) ? $latestPesanan : $latestPesanan->first();
    $totalQty = 0;
    if ($pesanan && $pesanan->keranjang && $pesanan->keranjang->queueKeranjang) {
        $totalQty = $pesanan->keranjang->queueKeranjang->sum('quantity');
    }
    // Satuan dari item pertama (seperti di Excel: 'Pail' untuk semua)
    $satuanTotal = '';
    if ($pesanan && $pesanan->keranjang && $pesanan->keranjang->queueKeranjang && count($pesanan->keranjang->queueKeranjang) > 0) {
        $satuanTotal = $pesanan->keranjang->queueKeranjang->first()->satuan ?? '';
    }
@endphp

<div class="action-buttons">
    <button class="btn-print" onclick="window.print()">🖨️ Cetak ke PDF</button>
    <a href="{{ route('filament.marketing.resources.pesanan.index') }}" class="btn-print btn-back">⬅️ Kembali</a>
</div>

@if($pesanan)
<div class="document-wrapper">

    <!-- ── HEADER ── -->
    <table class="header-table">
        <colgroup>
            <col class="col-company">
            <col class="col-label">
            <col class="col-colon">
            <col class="col-value">
        </colgroup>
        <tr>
            <td class="col-company">PT ANDALAN AGRO PERSADA</td>
            <td>Tanggal</td>
            <td>:</td>
            <td>{{ $pesanan->tanggal_terbit_surat_jalan
                    ? \Carbon\Carbon::parse($pesanan->tanggal_terbit_surat_jalan)->format('d-m-Y')
                    : date('d-m-Y') }}</td>
        </tr>
        <tr>
            <td>SAMARINDA</td>
            <td style="vertical-align: top;">Kepada Yth.</td>
            <td style="vertical-align: top;">:</td>
            <td>
                PT. {{ $pesanan->company_name ?? 'XXXX' }}<br>
                {{ $pesanan->address ?? 'xxxxxxx' }}<br>
                @if($pesanan->address2 ?? false)
                    &nbsp;&nbsp;{{ $pesanan->address2 }}<br>
                @endif
            </td>
        </tr>
    </table>

    <!-- ── JUDUL ── -->
    <div class="title-surat-jalan">SURAT JALAN</div>

    <!-- ── NO DO & PO ── -->
    <table class="no-table">
        <tr>
            <td class="col-no">NO : {{ $pesanan->no_delivery_order ?? '260255/AAP-DO/26' }}</td>
            <td class="col-po">PO : {{ $pesanan->no_po ?? 'XXXX' }}</td>
        </tr>
    </table>

    <!-- ── TABEL BARANG ── -->
    <table class="table-items">
        <thead>
            <tr>
                <th style="width:5%;">No</th>
                <th style="width:55%; text-align:left; padding-left:8px;">Nama Barang</th>
                <th style="width:20%;">Jumlah</th>
                <th style="width:20%;">Satuan</th>
            </tr>
        </thead>
        <tbody>
            @if($pesanan->keranjang && $pesanan->keranjang->queueKeranjang && count($pesanan->keranjang->queueKeranjang) > 0)
                @foreach($pesanan->keranjang->queueKeranjang as $index => $item)
                <tr>
                    <td class="col-no">{{ $index + 1 }}</td>
                    <td class="col-nama">{{ $item->item_name }}</td>
                    <td class="col-jumlah">{{ $item->quantity }}</td>
                    <td class="col-satuan">{{ $item->satuan }}</td>
                </tr>
                @endforeach
                {{-- Baris kosong agar tabel terisi sampai baris ke-21 (seperti Excel rows 12–31) --}}
                {{-- @php $fillerRows = max(0, 21 - count($pesanan->keranjang->queueKeranjang)); @endphp
                @for($i = 0; $i < $fillerRows; $i++)
                <tr>
                    <td class="col-no">&nbsp;</td>
                    <td class="col-nama">&nbsp;</td>
                    <td class="col-jumlah">&nbsp;</td>
                    <td class="col-satuan">&nbsp;</td>
                </tr>
                @endfor --}}
            @else
                @for($i = 0; $i < 21; $i++)
                <tr>
                    <td class="col-no">&nbsp;</td>
                    <td class="col-nama">&nbsp;</td>
                    <td class="col-jumlah">&nbsp;</td>
                    <td class="col-satuan">&nbsp;</td>
                </tr>
                @endfor
            @endif
        </tbody>
        <tfoot>
            <tr class="row-jumlah">
                <td class="col-label-jumlah" colspan="2" style="border-left:1px solid #000; border-right:none; text-align:right; padding-right:6px;">Jumlah</td>
                <td class="col-total-qty" style="border-left:1px solid #000; border-right:1px solid #000; text-align:center;">{{ $totalQty }}</td>
                <td class="col-total-satuan" style="border-left:1px solid #000; border-right:1px solid #000; text-align:center;">{{ $satuanTotal }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- ── TANDA TANGAN ── -->
    <table class="ttd-table">
        <!-- Header: Penerima | Pengirim | Hormat Kami -->
        <tr class="ttd-header">
            <td class="cell-penerima">Penerima</td>
            <td class="cell-pengirim">Pengirim</td>
            <td class="cell-hormat">Hormat Kami,</td>
        </tr>
        <!-- Ruang tanda tangan -->
        <tr class="ttd-space">
            <td style="border-left:1px solid #000; border-right:1px solid #000; width:33.33%; height:55px;"></td>
            <td style="border-left:1px solid #000; border-right:1px solid #000; width:33.33%;"></td>
            <td style="border-left:1px solid #000; border-right:1px solid #000; width:33.33%;"></td>
        </tr>
        <!-- Nama & Tanggal -->
        <tr class="ttd-info">
            <td>
                Nama &nbsp;&nbsp;&nbsp; :<br>
                Tanggal &nbsp;:
            </td>
            <td>
                Nama &nbsp;&nbsp;&nbsp; :<br>
                Tanggal &nbsp;:
            </td>
            <td>
                Nama &nbsp;&nbsp;&nbsp; : {{ $username  }}<br>
                Tanggal &nbsp;: {{ date('d-m-Y') }}
            </td>
        </tr>
    </table>

    <!-- ── NOTE ── -->
    <div class="note-section">
        <table class="note-table">
            <tr>
                <td class="note-label">NOTE</td>
                <td class="note-colon">:</td>
                <td class="note-lembar">Lembar Putih</td>
                <td class="note-colon2">:</td>
                <td>{{ $pesanan->company_name ?? 'PT AAP' }}</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td>Lembar Pink</td>
                <td>:</td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td>Lembar Kuning</td>
                <td>:</td>
                <td></td>
            </tr>
        </table>

        <div class="disclaimer">
            <div>*** Mohon diperiksa kembali keadaan dan jumlah barang sebelum diterima</div>
            <div>*** Barang yang sudah diterima dan telah ditanda tangan tidak dapat dikembalikan dan bukan</div>
            <div style="padding-left: 24px;">merupakah tanggung jawab kami</div>
        </div>
    </div>

</div>
@else
<p style="text-align:center; font-weight:bold;">Data Pesanan Tidak Ditemukan</p>
@endif

<script>
    window.onload = function () {
        window.print();
    };
</script>
</body>
</html>