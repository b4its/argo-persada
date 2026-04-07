<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Buku Besar - Mutasi</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, Helvetica, sans-serif; background-color: #ffffff; color: #000000; font-size: 12px; padding: 20px; }
        .action-buttons { display: flex; justify-content: center; align-items: center; gap: 15px; margin-bottom: 30px; }
        .btn { padding: 8px 16px; font-size: 14px; font-weight: bold; cursor: pointer; border: 1px solid #ccc; background-color: #f8f9fa; border-radius: 4px; transition: background-color 0.2s; }
        .btn:hover { background-color: #e2e6ea; }
        .user-info { font-size: 14px; color: #666; font-style: italic; }
        .report-header { text-align: center; margin-bottom: 25px; }
        .report-header h2 { font-size: 16px; margin-bottom: 5px; text-transform: capitalize; }
        .report-header h1 { font-size: 20px; color: #1e88e5; margin-bottom: 8px; }
        .report-header p { font-size: 12px; color: #b71c1c; font-weight: bold; }
        .account-section { margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        .account-header-row { background-color: #e5e7eb; }
        .account-header-row th { text-align: left; padding: 8px; color: #1e88e5; font-size: 13px; }
        .account-code { display: inline-block; width: 100px; }
        .col-header th { text-align: left; padding: 6px 8px; border-bottom: 1px solid #000; border-top: 1px solid #000; }
        td { padding: 4px 8px; vertical-align: top; }
        
        /* Tambahan class khusus untuk kolom keterangan agar teks panjang otomatis turun ke bawah */
        .col-keterangan { word-break: break-word; white-space: normal; overflow-wrap: break-word; }
        
        .col-number { text-align: right; width: 1%; white-space: nowrap; }
        .col-text { text-align: left; }
        .summary-row td { background-color: #e5e7eb; padding: 6px 8px; font-weight: normal; }
        .summary-label-container { display: flex; justify-content: space-between; max-width: 250px; }
        .summary-label-right { display: flex; justify-content: flex-end; gap: 20px; }
        .empty-state { text-align: center; padding: 40px; font-size: 14px; color: #666; font-style: italic; border: 1px dashed #ccc; margin-top: 20px; border-radius: 8px; }

        @media print {
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .no-print { display: none !important; }
            body { padding: 0; }
            @page { margin: 1cm; }
            .summary-row td, .account-header-row, .account-header-row th { background-color: #e5e7eb !important; }
            .empty-state { border: 1px solid #000 !important; color: #000 !important; }
        }
    </style>
</head>
<body>

    <div class="action-buttons no-print">
        <button class="btn" onclick="window.print()">Print Dokumen</button>
        <span class="user-info">Dicetak oleh: {{ $username ?? 'User' }}</span>
    </div>

    <div class="report-header">
        <h2>{{ $bukuBesar->name ?? 'Nama Buku Besar Tidak Ada' }}</h2>
        <h1>Buku Besar - Mutasi</h1>
        <p>
            @if($bukuBesar->periode)
                @php
                    $parsedDate = \Carbon\Carbon::parse($bukuBesar->periode);
                    // Gunakan copy() agar object asli tidak ikut berubah
                    $awalBulan = $parsedDate->copy()->startOfMonth()->translatedFormat('l, F d, Y');
                    $akhirBulan = $parsedDate->copy()->endOfMonth()->translatedFormat('l, F d, Y');
                @endphp
                {{ $awalBulan }} - {{ $akhirBulan }}
            @else
                Periode: -
            @endif
        </p>
    </div>

    @forelse ($bukuBesar->mutasis as $mutasi)
        @php
            // Karena DB sudah berubah, kita langsung ambil dari field $mutasi
            $saldoAwal = $mutasi->saldo_awal ?? 0;
            $saldoAkhir = $mutasi->saldo_akhir ?? 0;
            
            // Tetap lakukan perhitungan total debet kredit di UI sebagai validasi
            $totalDebet = $mutasi->mutasiItems->sum('debet');
            $totalKredit = $mutasi->mutasiItems->sum('kredit');
            $nilaiMutasi = $totalDebet - $totalKredit;
        @endphp

        <div class="account-section">
            <table>
                <thead>
                    <tr class="account-header-row">
                        <th colspan="7">
                            <span class="account-code">{{ $mutasi->code }}</span> <span>{{ $mutasi->name }}</span>
                        </th>
                    </tr>
                    <tr class="col-header">
                        <th width="80">Tanggal</th>
                        <th width="40">Tp</th>
                        <th width="90">No. Ref.</th>
                        <th class="col-keterangan">Keterangan</th>
                        <th class="col-number">Debet</th>
                        <th class="col-number">Kredit</th>
                        <th class="col-number">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="4">Saldo Awal:</td>
                        <td class="col-number"></td>
                        <td class="col-number"></td>
                        <td class="col-number">{{ number_format($saldoAwal, 2, '.', ',') }}</td>
                    </tr>
                    
                    @forelse ($mutasi->mutasiItems as $item)
                        <tr>
                            <td>{{ $item->created_at ? $item->created_at->format('d/m/Y') : '-' }}</td>
                            <td>-</td>
                            <td>{{ $item->no_ref }}</td>
                            <td class="col-keterangan">{{ $item->keterangan }}</td>
                            <td class="col-number">
                                {{ $item->debet > 0 ? number_format($item->debet, 2, '.', ',') : '' }}
                            </td>
                            <td class="col-number">
                                {{ $item->kredit > 0 ? number_format($item->kredit, 2, '.', ',') : '' }}
                            </td>
                            <td class="col-number">{{ number_format($item->saldo, 2, '.', ',') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; font-style: italic;">Tidak ada transaksi</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="summary-row">
                        <td colspan="4">
                            <div class="summary-label-container">
                                <strong>Saldo Awal :</strong> <span>{{ number_format($saldoAwal, 2, '.', ',') }}</span>
                            </div>
                        </td>
                        <td class="col-number">
                            <div class="summary-label-right">
                                <strong>Total :</strong> <span>{{ number_format($totalDebet, 2, '.', ',') }}</span>
                            </div>
                        </td>
                        <td class="col-number">{{ number_format($totalKredit, 2, '.', ',') }}</td>
                        <td></td>
                    </tr>
                    <tr class="summary-row">
                        <td colspan="4">
                            <div class="summary-label-container">
                                <strong>Saldo Akhir :</strong> <span>{{ number_format($saldoAkhir, 2, '.', ',') }}</span>
                            </div>
                        </td>
                        <td colspan="2" class="col-number">
                            <div class="summary-label-right" style="padding-right: 25px;">
                                <strong>Mutasi :</strong> <span>{{ number_format($nilaiMutasi, 2, '.', ',') }}</span>
                            </div>
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @empty 
        <div class="empty-state">
            Sedang tidak tersedia data pada mutasi ini
        </div>
    @endforelse 


    <script>
        window.print()
    </script>
</body>
</html>