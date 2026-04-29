<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan - Pengiriman Barang</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            color: #000;
            background: #f0f0f0;
            padding: 20px;
        }

        /* ── SCREEN ONLY CONTROLS ── */
        .screen-only {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            background: #fff;
            padding: 12px 20px;
            border-radius: 6px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.1);
        }

        .btn {
            padding: 8px 18px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            font-weight: bold;
            transition: background 0.15s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-back {
            background: #6c757d;
            color: #fff;
        }
        .btn-back:hover { background: #5a6268; }
        .btn-print {
            background: #007bff;
            color: #fff;
        }
        .btn-print:hover { background: #0056b3; }

        .orientation-label {
            font-size: 13px;
            font-weight: bold;
            color: #333;
            margin-left: 8px;
        }
        .orientation-select {
            padding: 6px 10px;
            border: 1px solid #bbb;
            border-radius: 4px;
            font-size: 13px;
            cursor: pointer;
        }

        /* ── PAGE WRAPPER ── */
        .page-wrapper {
            background: #fff;
            padding: 30px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.15);
            margin: 0 auto;
            transition: max-width 0.3s ease;
            /* Default portrait width */
            max-width: 210mm; 
            min-height: 297mm;
        }

        .document-wrapper {
            width: 100%;
        }

        /* ── HEADER ── */
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

        .indent-address {
            margin-left: 10px;
            display: block;
        }

        /* ── JUDUL SURAT JALAN ── */
        .title-surat-jalan {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin: 10px 0 6px 0;
            letter-spacing: 1px;
        }

        /* ── NOMOR DO & PO ── */
        .no-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
            font-weight: bold;
            font-size: 11pt;
        }
        .no-table td { padding: 2px 0; }
        .col-no-do { width: 55%; }
        .col-po-no { width: 45%; }

        /* ── TABEL BARANG ── */
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
        .table-items thead th {
            border: 1px solid #000;
            text-align: center;
            font-weight: bold;
            background: #fff;
        }
        .table-items tbody td.col-no {
            border-left: 1px solid #000;
            border-right: none;
            text-align: center;
            width: 5%;
        }
        .table-items tbody td.col-nama {
            border-left: 1px solid #000;
            border-right: none;
            text-align: left;
            width: 55%;
        }
        .table-items tbody td.col-jumlah {
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            text-align: center;
            width: 20%;
        }
        .table-items tbody td.col-satuan {
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            text-align: center;
            width: 20%;
        }
        .table-items tbody tr { height: 21px; }

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

        /* ── TANDA TANGAN ── */
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
        .ttd-header td.cell-penerima,
        .ttd-header td.cell-pengirim,
        .ttd-header td.cell-hormat {
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            width: 33.33%;
        }

        .ttd-space td {
            height: 55px;
            border-left: 1px solid #000;
            border-right: 1px solid #000;
        }

        .ttd-info td {
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 4px 8px;
            line-height: 1.7;
        }
        .ttd-flex-row {
            display: flex;
            justify-content: flex-start;
            gap: 10px;
        }
        .ttd-label { width: 55px; }

        /* ── CATATAN / NOTE ── */
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
        .disclaimer-indent {
            margin-left: 24px;
        }

        /* ── PRINT MEDIA QUERY ── */
        @media print {
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .screen-only {
                display: none !important;
            }
            .page-wrapper {
                box-shadow: none !important;
                padding: 0 !important;
                max-width: 100% !important;
                min-height: auto !important;
                margin: 0 !important;
            }
            * {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
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
    $satuanTotal = '';
    if ($pesanan && $pesanan->keranjang && $pesanan->keranjang->queueKeranjang && count($pesanan->keranjang->queueKeranjang) > 0) {
        $satuanTotal = $pesanan->keranjang->queueKeranjang->first()->satuan ?? '';
    }
@endphp

<!-- ── SCREEN ONLY CONTROLS ── -->
<div class="screen-only">
    <a href="{{ route('filament.marketing.resources.pesanan.index') }}" class="btn btn-back">&#8592; Kembali</a>
    <button class="btn btn-print" onclick="window.print()">&#128438; Cetak PDF</button>
    
    <label class="orientation-label" for="orientSelect">Orientasi:</label>
    <select class="orientation-select" id="orientSelect" onchange="setOrientation(this.value)">
        <option value="portrait" selected>Portrait (Vertikal)</option>
        <option value="landscape">Landscape (Horizontal)</option>
    </select>
</div>

<!-- ── DYNAMIC PAGE ORIENTATION STYLE ── -->
<style id="printOrientStyle">
    @page { size: A4 portrait; margin: 12mm 15mm 12mm 15mm; }
</style>

@if($pesanan)
<div class="page-wrapper" id="pageWrapper">
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
                <td class="col-company">{{ $latestPesanan->companyInternal->name ?? "PT ANDALAN AGRO PERSADA" }}</td>
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
                        <span class="indent-address">{{ $pesanan->address2 }}</span>
                    @endif
                </td>
            </tr>
        </table>

        <!-- ── JUDUL ── -->
        <div class="title-surat-jalan">SURAT JALAN</div>

        <!-- ── NO DO & PO ── -->
        <table class="no-table">
            <tr>
                <td class="col-no-do">NO : {{ $pesanan->no_delivery_order ?? '260255/AAP-DO/26' }}</td>
                <td class="col-po-no">PO : {{ $pesanan->no_po ?? 'XXXX' }}</td>
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
                    {{-- Row Filler Logic jika diperlukan --}}
                    {{-- @php $fillerRows = max(0, 21 - count($pesanan->keranjang->queueKeranjang)); @endphp
                    @for($i = 0; $i < $fillerRows; $i++)
                    <tr>
                        <td class="col-no"></td>
                        <td class="col-nama"></td>
                        <td class="col-jumlah"></td>
                        <td class="col-satuan"></td>
                    </tr>
                    @endfor --}}
                @else
                    @for($i = 0; $i < 21; $i++)
                    <tr>
                        <td class="col-no"></td>
                        <td class="col-nama"></td>
                        <td class="col-jumlah"></td>
                        <td class="col-satuan"></td>
                    </tr>
                    @endfor
                @endif
            </tbody>
            <tfoot>
                <tr class="row-jumlah">
                    <td class="col-label-jumlah" colspan="2">Jumlah</td>
                    <td class="col-total-qty">{{ $totalQty }}</td>
                    <td class="col-total-satuan">{{ $satuanTotal }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- ── TANDA TANGAN ── -->
        <table class="ttd-table">
            <tr class="ttd-header">
                <td class="cell-penerima">Penerima</td>
                <td class="cell-pengirim">Pengirim</td>
                <td class="cell-hormat">Hormat Kami,</td>
            </tr>
            <tr class="ttd-space">
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr class="ttd-info">
                <td>
                    <div class="ttd-flex-row"><span class="ttd-label">Nama</span><span>:</span></div>
                    <div class="ttd-flex-row"><span class="ttd-label">Tanggal</span><span>:</span></div>
                </td>
                <td>
                    <div class="ttd-flex-row"><span class="ttd-label">Nama</span><span>:</span></div>
                    <div class="ttd-flex-row"><span class="ttd-label">Tanggal</span><span>:</span></div>
                </td>
                <td>
                    <div class="ttd-flex-row"><span class="ttd-label">Nama</span><span>: {{ $username ?? '' }}</span></div>
                    <div class="ttd-flex-row"><span class="ttd-label">Tanggal</span><span>: {{ date('d-m-Y') }}</span></div>
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
                <div class="disclaimer-indent">merupakah tanggung jawab kami</div>
            </div>
        </div>

    </div>
</div>
@else
<p style="text-align:center; font-weight:bold; margin-top: 50px;">Data Pesanan Tidak Ditemukan</p>
@endif

<script>
    function setOrientation(val) {
        const styleEl = document.getElementById('printOrientStyle');
        const pageWrapper = document.getElementById('pageWrapper');
        
        if (val === 'landscape') {
            styleEl.textContent = '@page { size: A4 landscape; margin: 12mm 15mm 12mm 15mm; }';
            // Menyesuaikan lebar wrapper di layar menjadi landscape
            if(pageWrapper) pageWrapper.style.maxWidth = '297mm'; 
        } else {
            styleEl.textContent = '@page { size: A4 portrait; margin: 12mm 15mm 12mm 15mm; }';
            // Menyesuaikan lebar wrapper di layar menjadi portrait
            if(pageWrapper) pageWrapper.style.maxWidth = '210mm';
        }
    }

    window.onload = function () {
        // Set orientasi default ke portrait
        setOrientation('portrait');
        window.print();
    };
</script>
</body>
</html>