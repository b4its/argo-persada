<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Buku Besar</title>
    
    <style id="dynamic-print-style">
        @page { size: A4 portrait; margin: 10mm; }
    </style>

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; }
        body { background-color: #f0f2f5; color: #000; padding: 20px; }
        .action-buttons { max-width: 100%; margin: 0 auto 20px auto; display: flex; gap: 10px; justify-content: center; align-items: center; }
        button, select { padding: 10px 20px; font-size: 14px; font-weight: bold; border: 1px solid #ccc; border-radius: 6px; cursor: pointer; transition: all 0.3s; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        select { background-color: #fff; color: #333; outline: none; }
        select:focus { border-color: #0d6efd; }
        .btn-back { background-color: #6c757d; color: white; border: none; }
        .btn-back:hover { background-color: #5a6268; }
        .btn-print { background-color: #0d6efd; color: white; border: none; }
        .btn-print:hover { background-color: #0b5ed7; }
        .page { background: white; margin: 0 auto; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20mm; overflow-x: auto; transition: width 0.3s, min-height 0.3s; }
        .header-laporan { text-align: center; margin-bottom: 20px; }
        .header-laporan h2 { margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; vertical-align: middle; }
        th { text-align: center; font-weight: bold; }
        .bg-peach { background-color: #fce4d6 !important; }
        .bg-yellow { background-color: #ffff00 !important; }
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .fw-bold { font-weight: bold; }
        @media print {
            body { background-color: transparent; padding: 0; }
            .action-buttons { display: none; }
            .page { margin: 0; padding: 0; box-shadow: none; width: 100% !important; min-height: auto !important; }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        }
    </style>
</head>
<body>

    <div class="action-buttons no-print">
        <button class="btn-back" onclick="goBack()">⬅ Kembali</button>
        
        <select id="layoutOption" onchange="changeLayout()">
            <option value="portrait">📄 Vertikal (Portrait)</option>
            <option value="landscape">📝 Horizontal (Landscape)</option>
        </select>

        <button class="btn-print" onclick="printPage()">🖨 Print Laporan</button>
    </div>

    <div class="page" id="documentPage">
        <div class="header-laporan">
            <h2>BUKU BESAR</h2>
            <p>
                Periode: 
                @if(request('filter_type') == 'all') Keseluruhan 
                @elseif(request('filter_type') == 'year') Tahun {{ request('year') }}
                @elseif(request('filter_type') == 'month') Bulan {{ request('month') }} - Tahun {{ request('year') }}
                @elseif(request('filter_type') == 'day' || request('filter_type') == 'week') Tanggal {{ request('date') }}
                @elseif(request('filter_type') == 'custom') {{ request('start_date') }} s/d {{ request('end_date') }}
                @else Keseluruhan @endif
            </p>
        </div>

        <table>
            <thead>
                <tr>
                    <th rowspan="3" class="bg-peach" style="width: 8%;">NAMA PT</th>
                    <th rowspan="3" class="bg-yellow" style="width: 8%;">KODE AKUN</th>
                    <th rowspan="3" style="width: 24%;">NAMA AKUN</th>
                    <th colspan="2">SALDO AWAL</th>
                    <th colspan="2" class="bg-yellow">MUTASI</th>
                    <th colspan="2">SALDO AKHIR</th>
                </tr>
                <tr>
                    <th style="width: 10%;">DEBET</th>
                    <th style="width: 10%;">KREDIT</th>
                    <th class="bg-yellow" style="width: 10%;">DEBET</th>
                    <th class="bg-yellow" style="width: 10%;">KREDIT</th>
                    <th style="width: 10%;">DEBET</th>
                    <th style="width: 10%;">KREDIT</th>
                </tr>
                <tr>
                    <th>Rp.</th>
                    <th>Rp.</th>
                    <th class="bg-yellow">Rp.</th>
                    <th class="bg-yellow">Rp.</th>
                    <th>Rp.</th>
                    <th>Rp.</th>
                </tr>
            </thead>
            <tbody>
                @forelse($groupedData as $kategoriName => $items)
                    <tr>
                        <td class="bg-peach"></td>
                        <td class="bg-yellow"></td>
                        <td class="fw-bold">{{ $kategoriName }}</td>
                        <td></td><td></td>
                        <td class="bg-yellow"></td><td class="bg-yellow"></td>
                        <td></td><td></td>
                    </tr>

                    @foreach($items as $item)
                        <tr>
                            <td class="bg-peach">{{ $item['company_name'] }}</td>
                            <td class="bg-yellow text-right">{{ $item['kode_akun'] }}</td>
                            <td>{{ $item['nama_akun'] }}</td>
                            <td class="text-right">{{ $item['awal_debet'] > 0 ? number_format($item['awal_debet'], 0, ',', '.') : '-' }}</td>
                            <td class="text-right">{{ $item['awal_kredit'] > 0 ? number_format($item['awal_kredit'], 0, ',', '.') : '-' }}</td>
                            <td class="bg-yellow text-right">{{ $item['mutasi_debet'] > 0 ? number_format($item['mutasi_debet'], 0, ',', '.') : '-' }}</td>
                            <td class="bg-yellow text-right">{{ $item['mutasi_kredit'] > 0 ? number_format($item['mutasi_kredit'], 0, ',', '.') : '-' }}</td>
                            <td class="text-right">{{ $item['akhir_debet'] > 0 ? number_format($item['akhir_debet'], 0, ',', '.') : '-' }}</td>
                            <td class="text-right">{{ $item['akhir_kredit'] > 0 ? number_format($item['akhir_kredit'], 0, ',', '.') : '-' }}</td>
                        </tr>
                    @endforeach

                    <tr>
                        <td class="bg-peach"></td>
                        <td class="bg-yellow"></td>
                        <td style="color: transparent;">.</td>
                        <td></td><td></td>
                        <td class="bg-yellow"></td><td class="bg-yellow"></td>
                        <td></td><td></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">Tidak ada transaksi pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-center fw-bold bg-peach" style="background-color: transparent !important;">JUMLAH</th>
                    <th class="text-right">{{ $grandTotals['awal_debet'] > 0 ? number_format($grandTotals['awal_debet'], 0, ',', '.') : '-' }}</th>
                    <th class="text-right">{{ $grandTotals['awal_kredit'] > 0 ? number_format($grandTotals['awal_kredit'], 0, ',', '.') : '-' }}</th>
                    <th class="text-right bg-yellow">{{ $grandTotals['mutasi_debet'] > 0 ? number_format($grandTotals['mutasi_debet'], 0, ',', '.') : '-' }}</th>
                    <th class="text-right bg-yellow">{{ $grandTotals['mutasi_kredit'] > 0 ? number_format($grandTotals['mutasi_kredit'], 0, ',', '.') : '-' }}</th>
                    <th class="text-right">{{ $grandTotals['akhir_debet'] > 0 ? number_format($grandTotals['akhir_debet'], 0, ',', '.') : '-' }}</th>
                    <th class="text-right">{{ $grandTotals['akhir_kredit'] > 0 ? number_format($grandTotals['akhir_kredit'], 0, ',', '.') : '-' }}</th>
                </tr>
            </tfoot>
        </table>
    </div>

    <script>
        function changeLayout() {
            const layoutOption = document.getElementById("layoutOption").value;
            const pageElement = document.getElementById("documentPage");
            const dynamicStyle = document.getElementById("dynamic-print-style");

            if (layoutOption === "landscape") {
                pageElement.style.width = "297mm";
                pageElement.style.minHeight = "210mm";
                dynamicStyle.innerHTML = "@page { size: A4 landscape; margin: 10mm; }";
            } else {
                pageElement.style.width = "210mm";
                pageElement.style.minHeight = "297mm";
                dynamicStyle.innerHTML = "@page { size: A4 portrait; margin: 10mm; }";
            }
        }

        window.onload = function() {
            changeLayout();
        };

        function printPage() {
            window.print();
        }

        function goBack() {
            if(window.history.length > 1) {
                window.history.back();
            } else {
                alert("Tidak ada riwayat halaman sebelumnya.");
            }
        }
    </script>
</body>
</html>