<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Belanja - Invoice Surat PO</title>
    <style>
        /* ── RESET & DASAR ── */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
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
            max-width: 297mm; 
            min-height: 210mm;
            /* Tambahan: mencegah isi tumpah ke luar kotak putih di layar */
            overflow-x: auto; 
        }

        .document-wrapper {
            width: 100%;
            /* Pastikan tabel tidak terlalu mengerut dan merusak struktur huruf */
            min-width: 700px;
        }

        /* ── BAGIAN HEADER (INFORMASI PO) ── */
        .header-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 20px;
            letter-spacing: 1px;
            transition: font-size 0.3s ease;
        }
        
        .info-table {
            width: auto;
            margin-bottom: 20px;
            font-weight: bold;
            transition: font-size 0.3s ease;
        }
        .info-table td {
            padding: 4px 12px 4px 0;
            vertical-align: top;
        }

        /* ── TABEL DATA UTAMA ── */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            word-break: break-word; /* Mencegah teks panjang merusak layout */
        }
        .data-table th, 
        .data-table td {
            border: 1px solid #000;
            padding: 8px 6px;
            text-align: center;
            vertical-align: middle;
            font-size: 12px;
            transition: all 0.3s ease;
        }
        .data-table th {
            background-color: #f2f2f2;
            text-transform: uppercase;
            font-weight: bold;
        }
        
        .col-desc { text-align: left !important; }
        .col-number { text-align: right !important; }
        .col-center { text-align: center !important; }

        /* ── PENYESUAIAN KHUSUS MODE PORTRAIT (VERTIKAL) ── */
        /* Ketika class portrait-mode aktif, kita perkecil ukuran elemen agar muat di kertas A4 Vertikal */
        .page-wrapper.portrait-mode .document-wrapper {
            min-width: unset; /* Lepas min-width agar bisa mengecil sesuai kertas */
        }
        .page-wrapper.portrait-mode .header-title {
            font-size: 15px;
        }
        .page-wrapper.portrait-mode .info-table {
            font-size: 11px;
        }
        .page-wrapper.portrait-mode .data-table th, 
        .page-wrapper.portrait-mode .data-table td {
            font-size: 10px; /* Font dikecilkan secara signifikan */
            padding: 5px 3px; /* Padding dikurangi agar hemat ruang horizontal */
        }

        /* ── PENGATURAN KHUSUS SAAT PRINT (PDF) ── */
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
                overflow-x: visible !important;
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
        $pesanan = $latestPesanan;
    @endphp

    <div class="screen-only">
        <a href="{{ route('filament.marketing.resources.pesanan.index') }}" class="btn btn-back">&#8592; Kembali</a>
        <button class="btn btn-print" onclick="window.print()">&#128438; Cetak PDF</button>
        <a href="{{ route('export.surat_po', $pesanan->id) }}" class="btn" style="background: #217346; color: #fff;">
            <svg style="width:16px; height:16px; vertical-align: middle; margin-right: 4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Export Excel
        </a>
        
        <label class="orientation-label" for="orientSelect">Orientasi:</label>
        <select class="orientation-select" id="orientSelect" onchange="setOrientation(this.value)">
            <option value="landscape" selected>Landscape (Horizontal)</option>
            <option value="portrait">Portrait (Vertikal)</option>
        </select>
    </div>

    <style id="printOrientStyle">
        @page { size: A4 landscape; margin: 15mm; }
    </style>

    <div class="page-wrapper" id="pageWrapper">
        <div class="document-wrapper">
            
            <div class="header-title">
                DAFTAR BELANJA
            </div>

            @if($pesanan)
            <table class="info-table">
                <tr>
                    <td>PURCHASE REQUISITION NUMBER</td>
                    <td>:</td>
                    <td>{{ $pesanan->no_requisition ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>:</td>
                    <td>{{ $pesanan->created_at ? $pesanan->created_at->format('d-m-Y') : date('d-m-Y') }}</td>
                </tr>
                <tr>
                    <td>DEPARTMENT</td>
                    <td>:</td>
                    <td>{{ $pesanan->tipe_pesanan == 0 ? 'Supply' : 'Projek' }}</td>
                </tr>
            </table>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>Perusahaan</th>
                        <th>NO PO</th>
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
                    @if($pesanan->keranjang && $pesanan->keranjang->queueKeranjang && count($pesanan->keranjang->queueKeranjang) > 0)
                        @foreach($pesanan->keranjang->queueKeranjang as $index => $item)
                        <tr>
                            <td class="col-center">{{ $index + 1 }}</td>
                            <td class="col-center">{{ $pesanan->company_name }}</td>
                            <td class="col-center">{{ $pesanan->no_po ?? $pesanan->code }}</td>
                            
                            <td class="col-desc">{{ $item->item_name }}</td>
                            <td class="col-center">{{ $item->quantity }}</td>
                            <td class="col-center">{{ $item->satuan }}</td>
                            <td class="col-number">{{ number_format($item->po, 0, ',', '.') }}</td>
                            <td class="col-number">{{ number_format($item->modal, 0, ',', '.') }}</td>
                            <td class="col-number">{{ number_format($item->sub_total, 0, ',', '.') }}</td>
                            <td class="col-center">{{ $item->supplier_name }}</td>
                            <td class="col-center">{{ $item->keterangan }}</td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="11" class="col-center">Data rincian belanja tidak ditemukan.</td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="8" class="col-number">TOTAL KESELURUHAN</th>
                        <th class="col-number">{{ number_format($pesanan->total_harga ?? 0, 0, ',', '.') }}</th>
                        <th colspan="2"></th>
                    </tr>
                </tfoot>
            </table>
            @else
                <p style="text-align:center; font-weight:bold; margin-top: 50px;">Data Pesanan Tidak Ditemukan</p>
            @endif

        </div>
    </div>

    <script>
        function setOrientation(val) {
            const styleEl = document.getElementById('printOrientStyle');
            const pageWrapper = document.getElementById('pageWrapper');
            
            if (val === 'landscape') {
                styleEl.textContent = '@page { size: A4 landscape; margin: 15mm; }';
                if(pageWrapper) {
                    pageWrapper.style.maxWidth = '297mm'; 
                    // Hapus class portrait-mode agar tabel kembali ke ukuran normal
                    pageWrapper.classList.remove('portrait-mode'); 
                }
            } else {
                styleEl.textContent = '@page { size: A4 portrait; margin: 15mm; }';
                if(pageWrapper) {
                    pageWrapper.style.maxWidth = '210mm';
                    // Tambahkan class portrait-mode agar tabel dan font mengecil sesuai area vertikal
                    pageWrapper.classList.add('portrait-mode');
                }
            }
        }

        window.onload = function() {
            setOrientation('landscape'); // Set default orientasi
            // window.print(); // Uncomment baris ini jika ingin otomatis terbuka dialog print
        };
    </script>
</body>
</html>