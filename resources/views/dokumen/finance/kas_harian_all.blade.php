<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kas - {{ $kasHarian->first()->companyInternal->name ?? 'Semua PT' }}</title>
    <style>
        /* ── RESET & DASAR ── */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #000;
            background: #f3f4f6;
            padding: 20px;
        }

        /* ── SCREEN ONLY CONTROLS ── */
        .screen-only {
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            background: #fff;
            padding: 12px 20px;
            border-radius: 6px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.1);
            position: sticky;
            top: 20px;
            z-index: 50;
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
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-back { background: #4b5563; color: #fff; }
        .btn-back:hover { background: #374151; }
        .btn-print { background: #2563eb; color: #fff; }
        .btn-print:hover { background: #1d4ed8; }

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
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            margin: 0 auto;
            transition: max-width 0.3s ease;
            max-width: 297mm; 
            min-height: 210mm;
            overflow-x: auto;
        }

        .document-wrapper {
            width: 100%;
            min-width: 900px; /* Menjaga struktur di layar monitor */
        }

        /* ── HEADER DOKUMEN ── */
        .report-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 20px;
            text-transform: uppercase;
            transition: font-size 0.3s ease;
        }

        /* ── TABEL DATA UTAMA ── */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            word-break: break-word;
        }
        .data-table th, 
        .data-table td {
            border: 1px solid #374151;
            padding: 6px 8px;
            vertical-align: top;
            transition: all 0.3s ease;
        }
        .data-table th {
            background-color: #e5e7eb;
            text-transform: uppercase;
            font-weight: bold;
            text-align: center;
            font-size: 11px;
        }
        
        /* Utilitas Perataan */
        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }
        .text-left { text-align: left !important; }
        .whitespace-nowrap { white-space: nowrap; }

        /* ── PENYESUAIAN KHUSUS MODE PORTRAIT (LAYAR) ── */
        .page-wrapper.portrait-mode .document-wrapper {
            min-width: unset; 
        }
        .page-wrapper.portrait-mode .report-title {
            font-size: 15px;
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
                overflow: visible !important; /* Jangan sembunyikan overflow di print */
            }
            
            /* FIX UTAMA: Reset min-width saat masuk ke mode cetak kertas */
            .document-wrapper {
                min-width: 100% !important;
                width: 100% !important;
            }

            /* FIX UTAMA: Paksa font jadi sangat kecil agar 11 kolom muat di Portrait */
            .page-wrapper.portrait-mode .data-table th,
            .page-wrapper.portrait-mode .data-table td {
                font-size: 8px !important; 
                padding: 4px 2px !important;
                white-space: normal !important; /* Paksa teks membungkus ke bawah / tidak memanjang */
            }

            * {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    <div class="screen-only">
        <button onclick="window.history.back()" class="btn btn-back">
            <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </button>
        <button onclick="window.print()" class="btn btn-print">
            <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak PDF
        </button>
        <a href="{{ route('export.kas_harian_all', request()->all()) }}" class="btn" style="background: #217346; color: #fff;">
            <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Export Excel
        </a>
        <label class="orientation-label" for="orientSelect">Orientasi:</label>
        <select class="orientation-select" id="orientSelect" onchange="setOrientation(this.value)">
            <option value="landscape" selected>Landscape (Horizontal)</option>
            <option value="portrait">Portrait (Vertikal)</option>
        </select>
    </div>

    <style id="printOrientStyle">
        @page { size: A4 landscape; margin: 10mm; }
    </style>

    <div class="page-wrapper" id="pageWrapper">
        <div class="document-wrapper">
            
            <h1 class="report-title">DATA KAS {{ $kasHarian->first()->created_at->format('Y') ?? date('Y') }}</h1>

            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 8%">Tanggal</th>
                        <th style="width: 4%" class="whitespace-nowrap">PT</th>
                        <th style="width: 12%">NAMA</th>
                        <th style="width: 10%" class="whitespace-nowrap">PR/PO</th>
                        <th style="width: 10%">DB/FB</th>
                        <th style="width: 10%">TOKO</th>
                        <th style="width: 8%" class="whitespace-nowrap">KODE AKUN</th>
                        <th style="width: auto">KETERANGAN</th>
                        <th style="width: 9%" class="whitespace-nowrap">DEBET</th>
                        <th style="width: 9%" class="whitespace-nowrap">KREDIT</th>
                        <th style="width: 9%" class="whitespace-nowrap">SALDO</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kasHarian as $kas)
                        <tr>
                            <td class="text-center">{{ $kas->created_at->format('Y-m-d') }}</td>
                            <td class="text-center whitespace-nowrap">{{ $kas->companyInternal->singkatan ?? '-' }}</td>
                            <td>{{ $kas->user->name ?? '-' }}</td>
                            <td>{{ $kas->pesanan->no_requisition ?? '-' }}</td>
                            <td class="text-center">{{ $kas->pesanan->no_po ?? '-' }}</td>
                            <td>{{ $kas->toko ?? '-' }}</td>
                            <td class="text-center whitespace-nowrap">{{ $kas->akunKeuangan->kode ?? '-' }}</td>
                            <td>{{ $kas->keterangan ?? '-' }}</td>
                            <td class="text-right whitespace-nowrap">{{ isset($kas->debet) ? 'Rp ' . number_format($kas->debet, 0, ',', '.') : '-' }}</td>
                            <td class="text-right whitespace-nowrap">{{ isset($kas->kredit) ? 'Rp ' . number_format($kas->kredit, 0, ',', '.') : '-' }}</td>
                            <td class="text-right whitespace-nowrap">{{ isset($kas->saldo_akhir) ? 'Rp ' . number_format($kas->saldo_akhir, 0, ',', '.') : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center" style="padding: 16px; font-style: italic; color: #6b7280;">Tidak ada data Kas Harian yang tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>

    <script>
        function setOrientation(val) {
            const styleEl = document.getElementById('printOrientStyle');
            const pageWrapper = document.getElementById('pageWrapper');
            
            if (val === 'landscape') {
                styleEl.textContent = '@page { size: A4 landscape; margin: 10mm; }';
                if(pageWrapper) {
                    pageWrapper.style.maxWidth = '297mm'; 
                    pageWrapper.classList.remove('portrait-mode'); 
                }
            } else {
                styleEl.textContent = '@page { size: A4 portrait; margin: 10mm; }';
                if(pageWrapper) {
                    pageWrapper.style.maxWidth = '210mm';
                    pageWrapper.classList.add('portrait-mode');
                }
            }
        }

        window.onload = function() {
            setOrientation('landscape');
        };
    </script>
</body>
</html>