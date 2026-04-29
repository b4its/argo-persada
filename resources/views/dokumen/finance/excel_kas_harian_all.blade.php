<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Export Kas Harian</title>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th style="background-color: #e5e7eb; border: 1px solid #000000; text-align: center; font-weight: bold; vertical-align: middle;">Tanggal</th>
                <th style="background-color: #e5e7eb; border: 1px solid #000000; text-align: center; font-weight: bold; vertical-align: middle;">PT</th>
                <th style="background-color: #e5e7eb; border: 1px solid #000000; text-align: center; font-weight: bold; vertical-align: middle;">NAMA</th>
                <th style="background-color: #e5e7eb; border: 1px solid #000000; text-align: center; font-weight: bold; vertical-align: middle;">PR/PO</th>
                <th style="background-color: #e5e7eb; border: 1px solid #000000; text-align: center; font-weight: bold; vertical-align: middle;">DB/FB</th>
                <th style="background-color: #e5e7eb; border: 1px solid #000000; text-align: center; font-weight: bold; vertical-align: middle;">TOKO</th>
                <th style="background-color: #e5e7eb; border: 1px solid #000000; text-align: center; font-weight: bold; vertical-align: middle;">KODE AKUN</th>
                <th style="background-color: #e5e7eb; border: 1px solid #000000; text-align: center; font-weight: bold; vertical-align: middle;">KETERANGAN</th>
                <th style="background-color: #e5e7eb; border: 1px solid #000000; text-align: center; font-weight: bold; vertical-align: middle;">DEBET</th>
                <th style="background-color: #e5e7eb; border: 1px solid #000000; text-align: center; font-weight: bold; vertical-align: middle;">KREDIT</th>
                <th style="background-color: #e5e7eb; border: 1px solid #000000; text-align: center; font-weight: bold; vertical-align: middle;">SALDO</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kasHarian as $kas)
                <tr>
                    <td style="border: 1px solid #000000; text-align: center;">{{ $kas->created_at->format('Y-m-d') }}</td>
                    <td style="border: 1px solid #000000; text-align: center;">{{ $kas->companyInternal->singkatan ?? '-' }}</td>
                    <td style="border: 1px solid #000000; text-align: left;">{{ $kas->user->name ?? '-' }}</td>
                    <td style="border: 1px solid #000000; text-align: left;">{{ $kas->pesanan->no_requisition ?? '-' }}</td>
                    <td style="border: 1px solid #000000; text-align: center;">{{ $kas->pesanan->no_po ?? '-' }}</td>
                    <td style="border: 1px solid #000000; text-align: left;">{{ $kas->toko ?? '-' }}</td>
                    <td style="border: 1px solid #000000; text-align: center;">{{ $kas->akunKeuangan->kode ?? '-' }}</td>
                    <td style="border: 1px solid #000000; text-align: left;">{{ $kas->keterangan ?? '-' }}</td>
                    <td style="border: 1px solid #000000; text-align: right;">{{ $kas->debet ?? 0 }}</td>
                    <td style="border: 1px solid #000000; text-align: right;">{{ $kas->kredit ?? 0 }}</td>
                    <td style="border: 1px solid #000000; text-align: right;">{{ $kas->saldo_akhir ?? 0 }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>