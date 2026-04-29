<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Export Invoice Finance</title>
</head>
<body>
    <table>
        <!-- Header Informasi Invoice -->
        <tr>
            <th colspan="6" style="text-align: center; font-size: 16px; font-weight: bold; text-decoration: underline;">INVOICE</th>
        </tr>
        <tr>
            <td style="font-weight: bold;">Date</td>
            <td style="font-weight: bold;">:</td>
            <td style="text-align: left;">{{ $pesanan->tanggal_terbit_invoice ? \Carbon\Carbon::parse($pesanan->tanggal_terbit_invoice)->format('d-m-Y') : \Carbon\Carbon::now()->format('d-m-Y') }}</td>
            <td colspan="3" rowspan="2" style="font-weight: bold; vertical-align: top;">
                Kepada Yth.<br>
                {{ $pesanan->company_name ?? ($pesanan->group_name ?? '') }}<br>
                {{ $pesanan->address ?? '' }}
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold;">No</td>
            <td style="font-weight: bold;">:</td>
            <td style="text-align: left;">{{ $pesanan->no_invoice ?? $pesanan->no_requisition ?? '-' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">PO No</td>
            <td style="font-weight: bold;">:</td>
            <td style="text-align: left;">{{ $pesanan->no_po ?? '-' }}</td>
            <td colspan="3"></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Jatuh Tempo</td>
            <td style="font-weight: bold;">:</td>
            <td style="text-align: left;">{{ $pesanan->tanggal_jatuh_tempo ? \Carbon\Carbon::parse($pesanan->tanggal_jatuh_tempo)->format('d-m-Y') : '-' }}</td>
            <td colspan="3"></td>
        </tr>
        <tr>
            <td colspan="6"></td> <!-- Baris Kosong sebagai Spasi -->
        </tr>

        <!-- Tabel Data Barang -->
        <thead>
            <tr>
                <th style="background-color: #f8f9fa; border: 1px solid #000000; text-align: center; font-weight: bold; vertical-align: middle;">No</th>
                <th style="background-color: #f8f9fa; border: 1px solid #000000; text-align: center; font-weight: bold; vertical-align: middle;">Nama Barang</th>
                <th style="background-color: #f8f9fa; border: 1px solid #000000; text-align: center; font-weight: bold; vertical-align: middle;">Jumlah</th>
                <th style="background-color: #f8f9fa; border: 1px solid #000000; text-align: center; font-weight: bold; vertical-align: middle;">Satuan</th>
                <th style="background-color: #f8f9fa; border: 1px solid #000000; text-align: center; font-weight: bold; vertical-align: middle;">Harga Satuan (Rp.)</th>
                <th style="background-color: #f8f9fa; border: 1px solid #000000; text-align: center; font-weight: bold; vertical-align: middle;">Sub Total (Rp.)</th>
            </tr>
        </thead>
        <tbody>
            @if($pesanan->keranjang && $pesanan->keranjang->queueKeranjang && count($pesanan->keranjang->queueKeranjang) > 0)
                @foreach($pesanan->keranjang->queueKeranjang as $index => $item)
                <tr>
                    <td style="border: 1px solid #000000; text-align: center;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid #000000; text-align: left;">{{ $item->item_name }}</td>
                    <td style="border: 1px solid #000000; text-align: center;">{{ $item->quantity }}</td>
                    <td style="border: 1px solid #000000; text-align: center;">{{ $item->satuan }}</td>
                    <td style="border: 1px solid #000000; text-align: right;">{{ $item->po ?? 0 }}</td>
                    <td style="border: 1px solid #000000; text-align: right;">{{ $item->sub_total ?? 0 }}</td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="6" style="border: 1px solid #000000; text-align: center; font-style: italic;">Tidak ada data barang dalam pesanan ini.</td>
                </tr>
            @endif
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" rowspan="3"></td>
                <th colspan="3" style="border: 1px solid #000000; text-align: center; font-weight: bold;">Total</th>
                <th style="border: 1px solid #000000; text-align: right;">{{ $pesanan->keranjang->sub_total ?? 0 }}</th>
            </tr>
            <tr>
                <th colspan="3" style="border: 1px solid #000000; text-align: center; font-weight: bold;">PPN 11 %</th>
                <th style="border: 1px solid #000000; text-align: right;">{{ $pesanan->ppn ?? 0 }}</th>
            </tr>
            <tr>
                <th colspan="3" style="border: 1px solid #000000; text-align: center; font-weight: bold;">Total Pembayaran</th>
                <th style="border: 1px solid #000000; text-align: right; font-weight: bold;">{{ $pesanan->total_harga ?? 0 }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>