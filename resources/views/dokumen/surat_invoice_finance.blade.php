<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Invoice - PT Andalan Agro Persada</title>
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
        .btn-back { background: #6c757d; color: #fff; }
        .btn-back:hover { background: #5a6268; }
        .btn-print { background: #28a745; color: #fff; }
        .btn-print:hover { background: #218838; }

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
            padding: 40px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.15);
            margin: 0 auto;
            transition: max-width 0.3s ease;
            /* Default Portrait width */
            max-width: 210mm; 
            min-height: 297mm;
            overflow-x: auto; /* Mencegah overflow horizontal di layar */
        }

        .document-wrapper {
            width: 100%;
            min-width: 650px; /* Menjaga struktur tabel agar tidak hancur di layar kecil */
        }

        /* ── HEADER INVOICE ── */
        header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 24px;
        }
        header img {
            width: 96px;
            height: 96px;
            object-fit: contain;
        }
        header .company-info {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        header .company-info p {
            font-weight: bold;
            margin: 0;
            line-height: 1.6;
            font-size: 14px;
        }

        /* ── TABEL INVOICE UTAMA ── */
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        .invoice-table th, 
        .invoice-table td {
            border: 1px solid #000;
            padding: 6px 8px;
        }
        
        /* Tabel Inner untuk Detail Info (No Border) */
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            border: none;
            padding: 2px 4px 2px 0;
        }

        /* Utility Classes */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .valign-top { vertical-align: top; }
        
        .invoice-title {
            font-weight: bold;
            font-size: 16px;
            text-decoration: underline;
            margin: 0 0 8px 0;
            letter-spacing: 1px;
        }

        /* ── SYARAT KETENTUAN & TTD ── */
        .terms-list {
            margin: 0;
            padding: 0;
            list-style: none;
            line-height: 1.5;
        }
        .terms-list li { margin-bottom: 3px; }
        .bank-indent { padding-left: 14px; }

        .signature-box {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            height: 100%;
            min-height: 100px;
            padding-top: 10px;
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
        $items = \Illuminate\Support\Facades\DB::table('queue_keranjang')
                    ->where('keranjang_id', $latestPesanan->keranjang_id)
                    ->get();

        function penyebut($nilai) {
            $nilai = abs($nilai);
            $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
            $temp = "";
            if ($nilai < 12) { $temp = " ". $huruf[$nilai]; } 
            else if ($nilai < 20) { $temp = penyebut($nilai - 10). " belas"; } 
            else if ($nilai < 100) { $temp = penyebut($nilai/10)." puluh". penyebut($nilai % 10); } 
            else if ($nilai < 200) { $temp = " seratus" . penyebut($nilai - 100); } 
            else if ($nilai < 1000) { $temp = penyebut($nilai/100) . " ratus" . penyebut($nilai % 100); } 
            else if ($nilai < 2000) { $temp = " seribu" . penyebut($nilai - 1000); } 
            else if ($nilai < 1000000) { $temp = penyebut($nilai/1000) . " ribu" . penyebut($nilai % 1000); } 
            else if ($nilai < 1000000000) { $temp = penyebut($nilai/1000000) . " juta" . penyebut($nilai % 1000000); } 
            else if ($nilai < 1000000000000) { $temp = penyebut($nilai/1000000000) . " milyar" . penyebut(fmod($nilai,1000000000)); } 
            else if ($nilai < 1000000000000000) { $temp = penyebut($nilai/1000000000000) . " trilyun" . penyebut(fmod($nilai,1000000000000)); }     
            return $temp;
        }
    
        function terbilang($nilai) {
            $hasil = ($nilai < 0) ? "minus ". trim(penyebut($nilai)) : trim(penyebut($nilai));
            return ucfirst($hasil) . " Rupiah";
        }
    @endphp

    <!-- ── SCREEN ONLY CONTROLS ── -->
    <div class="screen-only">
        <button onclick="window.history.back()" class="btn btn-back">&#8592; Kembali</button>
        <button onclick="window.print()" class="btn btn-print">&#128438; Cetak Invoice</button>
        <a href="{{ route('export.invoice_finance', $latestPesanan->id) }}" class="btn" style="background: #217346; color: #fff;">
            <svg style="width:16px; height:16px; vertical-align: middle; margin-right: 4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Export Excel
        </a>
        <label class="orientation-label" for="orientSelect">Orientasi:</label>
        <select class="orientation-select" id="orientSelect" onchange="setOrientation(this.value)">
            <option value="portrait" selected>Portrait (Vertikal)</option>
            <option value="landscape">Landscape (Horizontal)</option>
        </select>
    </div>

    <!-- ── DYNAMIC PAGE ORIENTATION STYLE ── -->
    <style id="printOrientStyle">
        @page { size: A4 portrait; margin: 15mm; }
    </style>

    <div class="page-wrapper" id="pageWrapper">
        <div class="document-wrapper">
            
            <header>
                @php
                    $gambar_invoice = asset('images/logo.webp'); // Default awal
                    if ($latestPesanan->companyInternal && $latestPesanan->companyInternal->gambar) {
                        $gambar_invoice = asset($latestPesanan->companyInternal->gambar); 
                    }
                @endphp
                <img src="{{ $gambar_invoice }}" alt="Logo PT Andalan Agro Persada" />
                <div class="company-info">
                    <p>{{ $latestPesanan->companyInternal->name ?? "PT ANDALAN AGRO PERSADA" }}</p>
                    <p>{{ $latestPesanan->companyInternal->alamat ?? "Jl. D.I Panjaitan No 25 D"}}</p>
                    <p>Phone : {{ $latestPesanan->companyInternal->phone_number ?? "0541 2832313 / 7777993"}}</p>
                </div>
            </header>

            <table class="invoice-table">
                <tr>
                    <td colspan="2" class="valign-top">
                        <p class="invoice-title">INVOICE</p>
                        <table class="info-table">
                            <tr class="font-bold">
                                <td style="width: 90px">Date</td>
                                <td style="width: 15px">:</td>
                                <td>{{ $latestPesanan->tanggal_terbit_invoice ? \Carbon\Carbon::parse($latestPesanan->tanggal_terbit_invoice)->format('d-m-Y') : \Carbon\Carbon::now()->format('d-m-Y') }}</td>
                            </tr>
                            <tr class="font-bold">
                                <td>No</td>
                                <td>:</td>
                                <td>{{ $latestPesanan->no_invoice ?? $latestPesanan->no_requisition ?? '-' }}</td>
                            </tr>
                            <tr class="font-bold">
                                <td>PO No</td>
                                <td>:</td>
                                <td>{{ $latestPesanan->no_po ?? '-' }}</td>
                            </tr>
                            <tr class="font-bold">
                                <td>Jatuh Tempo</td>
                                <td>:</td>
                                <td>{{ $latestPesanan->tanggal_jatuh_tempo ? \Carbon\Carbon::parse($latestPesanan->tanggal_jatuh_tempo)->format('d-m-Y') : '-' }}</td>
                            </tr>
                        </table>
                    </td>

                    <td colspan="4" class="valign-top font-bold">
                        <p style="margin-bottom: 4px;">Kepada Yth.</p>
                        <p style="margin-bottom: 4px; font-size: 14px;">{{ $latestPesanan->company_name ?? $latestPesanan->group_name }}</p>
                        <p style="font-weight: normal; margin: 0;">{{ $latestPesanan->address ?? '' }}</p>
                    </td>
                </tr>

                <tr class="text-center font-bold" style="background-color: #f8f9fa;">
                    <th style="width: 5%">No</th>
                    <th style="width: 40%">Nama Barang</th>
                    <th style="width: 10%">Jumlah</th>
                    <th style="width: 10%">Satuan</th>
                    <th style="width: 17.5%">Harga Satuan (Rp.)</th>
                    <th style="width: 17.5%">Sub Total (Rp.)</th>
                </tr>

                @forelse($items as $index => $item)
                <tr style="height: 28px">
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->item_name }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-center">{{ $item->satuan }}</td>
                    <td class="text-right">{{ number_format($item->po, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->sub_total, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr style="height: 28px">
                    <td colspan="6" class="text-center" style="font-style: italic;">Tidak ada data barang dalam pesanan ini.</td>
                </tr>
                @endforelse

                <tr>
                    <td colspan="2" rowspan="3"></td>
                    <td colspan="3" class="text-center font-bold">Total</td>
                    <td class="text-right">{{ number_format($latestPesanan->keranjang->sub_total ?? 0, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="text-center font-bold">PPN 11 %</td>
                    <td class="text-right">{{ number_format($latestPesanan->ppn ?? 0, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="text-center font-bold">Total Pembayaran</td>
                    <td class="text-right font-bold">{{ number_format($latestPesanan->total_harga ?? 0, 0, ',', '.') }}</td>
                </tr>

                <tr>
                    <td colspan="6" style="padding: 16px 8px;">
                        <span class="font-bold">Terbilang:</span> <em>{{ terbilang($latestPesanan->total_harga ?? 0) }}</em>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" class="valign-top" style="padding: 12px 8px;">
                        <p class="font-bold" style="margin-bottom: 8px;">Term And Conditions :</p>
                        <ul class="terms-list">
                            <li>- Barang yang sudah dibeli tidak dapat dikembalikan atau ditukar</li>
                            <li>- Tidak menerima pembayaran tunai, pembayaran dilakukan dengan transfer ke rekening berikut :</li>
                            <li class="bank-indent font-bold">{{ $latestPesanan->companyInternal->nama_bank ?? 'Bank Mandiri' }}</li>
                            <li class="bank-indent font-bold">A/n : {{ $latestPesanan->companyInternal->nama_pemilik_bank ?? '-' }}</li>
                            <li class="bank-indent font-bold">No Rek : {{ $latestPesanan->companyInternal->no_rekening ?? 'xxxx' }}</li>
                            <li>- Pembayaran dengan Giro/Cek dianggap sah apabila sudah diterima di rekening kami</li>
                        </ul>
                    </td>

                    <td colspan="4" class="valign-top" style="padding: 12px 8px;">
                        <div class="signature-box">
                            <div class="text-center">
                                <p style="margin-bottom: 4px;">Hormat Kami,</p>
                                <p class="font-bold">{{ $latestPesanan->companyInternal->name ?? "PT Andalan Agro Persada" }}</p>
                            </div>
                            <div class="text-center" style="margin-top: 60px;">
                                <p>( {{ Auth::user()->name ?? 'Nama Pembuat' }} )</p>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>

        </div>
    </div>

    <script>
        function setOrientation(val) {
            const styleEl = document.getElementById('printOrientStyle');
            const pageWrapper = document.getElementById('pageWrapper');
            
            if (val === 'landscape') {
                styleEl.textContent = '@page { size: A4 landscape; margin: 15mm; }';
                if(pageWrapper) pageWrapper.style.maxWidth = '297mm'; 
            } else {
                styleEl.textContent = '@page { size: A4 portrait; margin: 15mm; }';
                if(pageWrapper) pageWrapper.style.maxWidth = '210mm';
            }
        }

        window.onload = function() {
            setOrientation('portrait'); // Set orientasi default ke portrait
            // window.print(); // Uncomment ini jika ingin kotak dialog print langsung terbuka saat direfresh
        };
    </script>
</body>
</html>