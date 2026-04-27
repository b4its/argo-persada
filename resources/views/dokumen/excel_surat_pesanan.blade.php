<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export PO</title>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th rowspan="2" style="background-color: #9DC3E6; border: 1px solid #000000; text-align: center; vertical-align: middle;">No</th>
                <th style="background-color: #9DC3E6; border: 1px solid #000000; text-align: center; vertical-align: middle;">Tggl PO</th>
                <th rowspan="2" style="background-color: #9DC3E6; border: 1px solid #000000; text-align: center; vertical-align: middle;">Group</th>
                <th rowspan="2" style="background-color: #9DC3E6; border: 1px solid #000000; text-align: center; vertical-align: middle;">Company</th>
                <th rowspan="2" style="background-color: #9DC3E6; border: 1px solid #000000; text-align: center; vertical-align: middle;">No PO</th>
                <th rowspan="2" style="background-color: #9DC3E6; border: 1px solid #000000; text-align: center; vertical-align: middle;">Nama Barang</th>
                <th rowspan="2" style="background-color: #9DC3E6; border: 1px solid #000000; text-align: center; vertical-align: middle;">Qty</th>
                <th rowspan="2" style="background-color: #9DC3E6; border: 1px solid #000000; text-align: center; vertical-align: middle;">STN</th>
                <th colspan="2" style="background-color: #9DC3E6; border: 1px solid #000000; text-align: center; vertical-align: middle;">H. PO</th>
                <th rowspan="2" style="background-color: #9DC3E6; border: 1px solid #000000; text-align: center; vertical-align: middle;">H. Modal</th>
                <th rowspan="2" style="background-color: #9DC3E6; border: 1px solid #000000; text-align: center; vertical-align: middle;">TOTAL</th>
                <th rowspan="2" style="background-color: #9DC3E6; border: 1px solid #000000; text-align: center; vertical-align: middle;">Suplier</th>
                <th rowspan="2" style="background-color: #9DC3E6; border: 1px solid #000000; text-align: center; vertical-align: middle;">NOMOR DB/PO</th>
                <th style="background-color: #00FF99; border: 1px solid #000000; text-align: center; vertical-align: middle;">TANGGAL RILIS</th>
                <th rowspan="2" style="background-color: #FFC000; border: 1px solid #000000; text-align: center; vertical-align: middle;">NOMOR DO</th>
                <th rowspan="2" style="background-color: #FFC000; border: 1px solid #000000; text-align: center; vertical-align: middle;">Tanggal DO</th>
                <th style="background-color: #FFB6C1; border: 1px solid #000000; text-align: center; vertical-align: middle;">TANGGAL</th>
                <th style="background-color: #FFC000; border: 1px solid #000000; text-align: center; vertical-align: middle;">Tanggal</th>
                <th style="background-color: #FFC000; border: 1px solid #000000; text-align: center; vertical-align: middle;">Nomor</th>
                <th style="background-color: #FFC000; border: 1px solid #000000; text-align: center; vertical-align: middle;">Tanggal</th>
                <th rowspan="2" style="background-color: #00B0F0; border: 1px solid #000000; text-align: center; vertical-align: middle;">Ket</th>
            </tr>
            <tr>
                <th style="background-color: #9DC3E6; border: 1px solid #000000; text-align: center;">Masuk</th>
                <th style="background-color: #9DC3E6; border: 1px solid #000000; text-align: center;">H. BARANG</th>
                <th style="background-color: #9DC3E6; border: 1px solid #000000; text-align: center;">TOTAL</th>
                <th style="background-color: #00FF99; border: 1px solid #000000; text-align: center;">DANA</th>
                <th style="background-color: #FFB6C1; border: 1px solid #000000; text-align: center;">KEMBALI DO</th>
                <th style="background-color: #FFC000; border: 1px solid #000000; text-align: center;">Invoice</th>
                <th style="background-color: #FFC000; border: 1px solid #000000; text-align: center;">Invoice</th>
                <th style="background-color: #FFC000; border: 1px solid #000000; text-align: center;">Lunas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dataFromDB as $index => $d)
                @php
                    $total_po = ($d['qty'] && $d['h_po']) ? $d['qty'] * $d['h_po'] : 0;
                    $total_modal = ($d['qty'] && $d['h_modal']) ? $d['qty'] * $d['h_modal'] : 0;
                @endphp
                <tr>
                    <td style="border: 1px solid #000000; text-align: center;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid #000000; text-align: center;">{{ $d['tgl_po'] }}</td>
                    <td style="border: 1px solid #000000; text-align: center;">{{ $d['group'] }}</td>
                    <td style="border: 1px solid #000000; text-align: center;">{{ $d['company'] }}</td>
                    <td style="border: 1px solid #000000; text-align: center;">{{ $d['no_po'] }}</td>
                    <td style="border: 1px solid #000000; text-align: left;">{{ $d['nama_barang'] }}</td>
                    <td style="border: 1px solid #000000; text-align: center;">{{ $d['qty'] }}</td>
                    <td style="border: 1px solid #000000; text-align: center;">{{ $d['stn'] }}</td>
                    <td style="border: 1px solid #000000; text-align: right;">{{ $d['h_po'] }}</td>
                    <td style="border: 1px solid #000000; text-align: right;">{{ $total_po }}</td>
                    <td style="border: 1px solid #000000; text-align: right;">{{ $d['h_modal'] }}</td>
                    <td style="border: 1px solid #000000; text-align: right;">{{ $total_modal }}</td>
                    <td style="border: 1px solid #000000; text-align: center;">{{ $d['suplier'] }}</td>
                    <td style="border: 1px solid #000000; text-align: center;">{{ $d['no_db'] }}</td>
                    <td style="border: 1px solid #000000; text-align: center;">{{ $d['tgl_rilis'] }}</td>
                    <td style="border: 1px solid #000000; text-align: center;">{{ $d['no_do'] }}</td>
                    <td style="border: 1px solid #000000; text-align: center;">{{ $d['tgl_do'] }}</td>
                    <td style="border: 1px solid #000000; text-align: center;">{{ $d['tgl_kembali'] }}</td>
                    <td style="border: 1px solid #000000; text-align: center;">{{ $d['tgl_inv'] }}</td>
                    <td style="border: 1px solid #000000; text-align: center;">{{ $d['no_inv'] }}</td>
                    <td style="border: 1px solid #000000; text-align: center;">{{ $d['tgl_lunas'] }}</td>
                    <td style="border: 1px solid #000000; text-align: center;">{{ $d['ket'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>