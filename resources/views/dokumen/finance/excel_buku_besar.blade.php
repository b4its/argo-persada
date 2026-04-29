<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Export Buku Besar</title>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th rowspan="3" style="background-color: #fce4d6; border: 1px solid #000000; text-align: center; vertical-align: middle; font-weight: bold;">NAMA PT</th>
                <th rowspan="3" style="background-color: #ffff00; border: 1px solid #000000; text-align: center; vertical-align: middle; font-weight: bold;">KODE AKUN</th>
                <th rowspan="3" style="border: 1px solid #000000; text-align: center; vertical-align: middle; font-weight: bold;">NAMA AKUN</th>
                <th colspan="2" style="border: 1px solid #000000; text-align: center; vertical-align: middle; font-weight: bold;">SALDO AWAL</th>
                <th colspan="2" style="background-color: #ffff00; border: 1px solid #000000; text-align: center; vertical-align: middle; font-weight: bold;">MUTASI</th>
                <th colspan="2" style="border: 1px solid #000000; text-align: center; vertical-align: middle; font-weight: bold;">SALDO AKHIR</th>
            </tr>
            <tr>
                <th style="border: 1px solid #000000; text-align: center; font-weight: bold;">DEBET</th>
                <th style="border: 1px solid #000000; text-align: center; font-weight: bold;">KREDIT</th>
                <th style="background-color: #ffff00; border: 1px solid #000000; text-align: center; font-weight: bold;">DEBET</th>
                <th style="background-color: #ffff00; border: 1px solid #000000; text-align: center; font-weight: bold;">KREDIT</th>
                <th style="border: 1px solid #000000; text-align: center; font-weight: bold;">DEBET</th>
                <th style="border: 1px solid #000000; text-align: center; font-weight: bold;">KREDIT</th>
            </tr>
            <tr>
                <th style="border: 1px solid #000000; text-align: center; font-weight: bold;">Rp.</th>
                <th style="border: 1px solid #000000; text-align: center; font-weight: bold;">Rp.</th>
                <th style="background-color: #ffff00; border: 1px solid #000000; text-align: center; font-weight: bold;">Rp.</th>
                <th style="background-color: #ffff00; border: 1px solid #000000; text-align: center; font-weight: bold;">Rp.</th>
                <th style="border: 1px solid #000000; text-align: center; font-weight: bold;">Rp.</th>
                <th style="border: 1px solid #000000; text-align: center; font-weight: bold;">Rp.</th>
            </tr>
        </thead>
        <tbody>
            @forelse($groupedData as $kategoriName => $items)
                <tr>
                    <td style="background-color: #fce4d6; border: 1px solid #000000;"></td>
                    <td style="background-color: #ffff00; border: 1px solid #000000;"></td>
                    <td style="border: 1px solid #000000; font-weight: bold;">{{ $kategoriName }}</td>
                    <td style="border: 1px solid #000000;"></td>
                    <td style="border: 1px solid #000000;"></td>
                    <td style="background-color: #ffff00; border: 1px solid #000000;"></td>
                    <td style="background-color: #ffff00; border: 1px solid #000000;"></td>
                    <td style="border: 1px solid #000000;"></td>
                    <td style="border: 1px solid #000000;"></td>
                </tr>

                @foreach($items as $item)
                    <tr>
                        <td style="background-color: #fce4d6; border: 1px solid #000000;">{{ $item['company_name'] }}</td>
                        <td style="background-color: #ffff00; border: 1px solid #000000; text-align: right;">{{ $item['kode_akun'] }}</td>
                        <td style="border: 1px solid #000000;">{{ $item['nama_akun'] }}</td>
                        <td style="border: 1px solid #000000; text-align: right;">{{ $item['awal_debet'] > 0 ? $item['awal_debet'] : '-' }}</td>
                        <td style="border: 1px solid #000000; text-align: right;">{{ $item['awal_kredit'] > 0 ? $item['awal_kredit'] : '-' }}</td>
                        <td style="background-color: #ffff00; border: 1px solid #000000; text-align: right;">{{ $item['mutasi_debet'] > 0 ? $item['mutasi_debet'] : '-' }}</td>
                        <td style="background-color: #ffff00; border: 1px solid #000000; text-align: right;">{{ $item['mutasi_kredit'] > 0 ? $item['mutasi_kredit'] : '-' }}</td>
                        <td style="border: 1px solid #000000; text-align: right;">{{ $item['akhir_debet'] > 0 ? $item['akhir_debet'] : '-' }}</td>
                        <td style="border: 1px solid #000000; text-align: right;">{{ $item['akhir_kredit'] > 0 ? $item['akhir_kredit'] : '-' }}</td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="9" style="border: 1px solid #000000; text-align: center;">Tidak ada transaksi pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" style="border: 1px solid #000000; text-align: center; font-weight: bold;">JUMLAH</th>
                <th style="border: 1px solid #000000; text-align: right; font-weight: bold;">{{ $grandTotals['awal_debet'] > 0 ? $grandTotals['awal_debet'] : '-' }}</th>
                <th style="border: 1px solid #000000; text-align: right; font-weight: bold;">{{ $grandTotals['awal_kredit'] > 0 ? $grandTotals['awal_kredit'] : '-' }}</th>
                <th style="background-color: #ffff00; border: 1px solid #000000; text-align: right; font-weight: bold;">{{ $grandTotals['mutasi_debet'] > 0 ? $grandTotals['mutasi_debet'] : '-' }}</th>
                <th style="background-color: #ffff00; border: 1px solid #000000; text-align: right; font-weight: bold;">{{ $grandTotals['mutasi_kredit'] > 0 ? $grandTotals['mutasi_kredit'] : '-' }}</th>
                <th style="border: 1px solid #000000; text-align: right; font-weight: bold;">{{ $grandTotals['akhir_debet'] > 0 ? $grandTotals['akhir_debet'] : '-' }}</th>
                <th style="border: 1px solid #000000; text-align: right; font-weight: bold;">{{ $grandTotals['akhir_kredit'] > 0 ? $grandTotals['akhir_kredit'] : '-' }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>