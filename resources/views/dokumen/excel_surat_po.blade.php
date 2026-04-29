<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Export Surat PO</title>
</head>
<body>
    <table>
        <!-- Header Informasi Surat -->
        <tr>
            <th colspan="11" style="text-align: center; font-size: 16px; font-weight: bold;">DAFTAR BELANJA</th>
        </tr>
        <tr>
            <td colspan="3" style="font-weight: bold;">PURCHASE REQUISITION NUMBER</td>
            <td style="font-weight: bold;">:</td>
            <td colspan="7" style="text-align: left;">{{ $pesanan->no_requisition ?? '-' }}</td>
        </tr>
        <tr>
            <td colspan="3" style="font-weight: bold;">Tanggal Supply</td>
            <td style="font-weight: bold;">:</td>
            <td colspan="7" style="text-align: left;">{{ $pesanan->created_at ? $pesanan->created_at->format('d-m-Y') : date('d-m-Y') }}</td>
        </tr>
        <tr>
            <td colspan="3" style="font-weight: bold;">DEPARTMENT</td>
            <td style="font-weight: bold;">:</td>
            <td colspan="7" style="text-align: left;">{{ $pesanan->group_name ?? 'SUPPLY' }}</td>
        </tr>
        <tr>
            <td colspan="11"></td> <!-- Baris Kosong sebagai Spasi -->
        </tr>

        <!-- Tabel Data Barang -->
        <thead>
            <tr>
                <th style="background-color: #f2f2f2; border: 1px solid #000000; text-align: center; font-weight: bold; vertical-align: middle;">NO</th>
                <th style="background-color: #f2f2f2; border: 1px solid #000000; text-align: center; font-weight: bold; vertical-align: middle;">PT</th>
                <th style="background-color: #f2f2f2; border: 1px solid #000000; text-align: center; font-weight: bold; vertical-align: middle;">NO PO</th>
                <th style="background-color: #f2f2f2; border: 1px solid #000000; text-align: center; font-weight: bold; vertical-align: middle;">Deskripsi</th>
                <th style="background-color: #f2f2f2; border: 1px solid #000000; text-align: center; font-weight: bold; vertical-align: middle;">Qty</th>
                <th style="background-color: #f2f2f2; border: 1px solid #000000; text-align: center; font-weight: bold; vertical-align: middle;">Satuan</th>
                <th style="background-color: #f2f2f2; border: 1px solid #000000; text-align: center; font-weight: bold; vertical-align: middle;">PO</th>
                <th style="background-color: #f2f2f2; border: 1px solid #000000; text-align: center; font-weight: bold; vertical-align: middle;">MODAL</th>
                <th style="background-color: #f2f2f2; border: 1px solid #000000; text-align: center; font-weight: bold; vertical-align: middle;">TOTAL</th>
                <th style="background-color: #f2f2f2; border: 1px solid #000000; text-align: center; font-weight: bold; vertical-align: middle;">Supplier</th>
                <th style="background-color: #f2f2f2; border: 1px solid #000000; text-align: center; font-weight: bold; vertical-align: middle;">KET</th>
            </tr>
        </thead>
        <tbody>
            @if($pesanan->keranjang && $pesanan->keranjang->queueKeranjang && count($pesanan->keranjang->queueKeranjang) > 0)
                @foreach($pesanan->keranjang->queueKeranjang as $index => $item)
                <tr>
                    <td style="border: 1px solid #000000; text-align: center;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid #000000; text-align: center;">{{ $index === 0 ? $pesanan->company_name : '' }}</td>
                    <td style="border: 1px solid #000000; text-align: center;">{{ $index === 0 ? $pesanan->no_po : '' }}</td>
                    <td style="border: 1px solid #000000; text-align: left;">{{ $item->item_name }}</td>
                    <td style="border: 1px solid #000000; text-align: center;">{{ $item->quantity }}</td>
                    <td style="border: 1px solid #000000; text-align: center;">{{ $item->satuan }}</td>
                    <td style="border: 1px solid #000000; text-align: right;">{{ $item->po ?? 0 }}</td>
                    <td style="border: 1px solid #000000; text-align: right;">{{ $item->modal ?? 0 }}</td>
                    <td style="border: 1px solid #000000; text-align: right;">{{ $item->sub_total ?? 0 }}</td>
                    <td style="border: 1px solid #000000; text-align: center;">{{ $item->supplier_name }}</td>
                    <td style="border: 1px solid #000000; text-align: left;">{{ $item->keterangan }}</td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="11" style="border: 1px solid #000000; text-align: center;">Data rincian belanja tidak ditemukan.</td>
                </tr>
            @endif
        </tbody>
        <tfoot>
            <tr>
                <th colspan="8" style="border: 1px solid #000000; text-align: right; font-weight: bold;">TOTAL KESELURUHAN</th>
                <th style="border: 1px solid #000000; text-align: right; font-weight: bold;">{{ $pesanan->total_harga ?? 0 }}</th>
                <th colspan="2" style="border: 1px solid #000000;"></th>
            </tr>
        </tfoot>
    </table>
</body>
</html>