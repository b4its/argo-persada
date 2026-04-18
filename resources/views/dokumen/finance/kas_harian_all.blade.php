<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kas - {{ $kasHarian->first()->companyInternal->name ?? 'Semua PT' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Konfigurasi Default Kertas A4 */
        @page {
            size: A4 portrait; 
            margin: 15mm;
        }

        /* Tampilan Mode Print - Menyembunyikan tombol aksi */
        @media print {
            .no-print { display: none !important; }
            body { background: white; margin: 0; padding: 0; }
            .a4-preview { 
                box-shadow: none; 
                margin: 0; 
                padding: 0; 
                width: 100%; 
                max-width: none; 
            }
        }

        /* Tampilan Layar (Mockup Kertas A4) */
        body { background-color: #f3f4f6; font-family: Arial, sans-serif; }
        
        .a4-preview {
            background: white;
            width: 210mm; /* Lebar default portrait */
            min-height: 297mm;
            margin: 40px auto;
            padding: 20px 30px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            transition: width 0.3s ease;
        }

        /* Styling Tabel */
        .table-cell { 
            border: 1px solid #374151; 
            padding: 8px 12px; 
            font-size: 12px; 
            vertical-align: top;
        }
        .table-header { 
            background-color: #e5e7eb; 
            font-weight: bold; 
            text-align: center; 
            text-transform: uppercase;
            font-size: 11px;
        }
    </style>
</head>
<body>

    <div class="no-print bg-white p-4 shadow-md flex gap-4 justify-center sticky top-0 z-50 border-b">
        <button onclick="window.history.back()" class="px-5 py-2.5 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </button>
        <button onclick="triggerPrint('portrait')" class="px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Print Vertikal
        </button>
        <button onclick="triggerPrint('landscape')" class="px-5 py-2.5 bg-emerald-600 text-white font-medium rounded-lg hover:bg-emerald-700 transition flex items-center gap-2">
            <svg class="w-4 h-4 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Print Horizontal
        </button>
    </div>

    <div class="a4-preview" id="documentArea">
        {{-- <h1 class="text-2xl font-bold text-center mb-6 text-gray-800">DATA KAS {{ $kasHarian->first()->created_at->format('Y') ?? now()->format('Y') }}</h1> --}}
        
        {{-- <div class="mb-4 text-sm text-gray-600 flex justify-between">
            <div>
                <p>Dicetak oleh: <strong>{{ $username }}</strong></p>
                <p>Tanggal Cetak: <strong>{{ now()->format('Y-m-d H:i') }}</strong></p>
            </div>
            <div>
                <p>Total Data: <strong>{{ $kasHarian->count() }} Baris</strong></p>
            </div>
        </div> --}}

        <table class="w-full border-collapse">
            <thead>
                <tr>
                    <th class="table-cell table-header" style="width: 10%">Tanggal</th>
                    <th class="table-cell table-header whitespace-nowrap" style="width: 1%">PT</th>
                    <th class="table-cell table-header" style="width: auto">Nama</th>
                    <th class="table-cell table-header" style="width: 15%">PR/PO</th>
                    <th class="table-cell table-header" style="width: 10%">DB/FB</th>
                    <th class="table-cell table-header" style="width: 10%">TOKO</th>
                    <th class="table-cell table-header whitespace-nowrap" style="width: 1%">DEBET</th>
                    <th class="table-cell table-header whitespace-nowrap" style="width: 1%">KREDIT</th>
                    <th class="table-cell table-header" style="width: auto">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($kasHarian as $kas)
                    <tr>
                        <td class="table-cell text-center">{{ $kas->created_at->format('Y-m-d') }}</td>
                        <td class="table-cell text-center whitespace-nowrap">{{ $kas->companyInternal->singkatan ?? '-' }}</td>
                        <td class="table-cell">{{ $kas->user->name ?? '-' }}</td>
                        <td class="table-cell">{{ $kas->pesanan->no_requisition ?? '-' }}</td>
                        <td class="table-cell text-center">{{ $kas->pesanan->code ?? '-' }}</td>
                        <td class="table-cell">{{ $kas->pesanan->group_name ?? '-' }}</td>
                        <td class="table-cell text-right whitespace-nowrap">{{ isset($kas->debet) ? 'Rp ' . number_format($kas->debet, 0, ',', '.') : '-' }}</td>
                        <td class="table-cell text-right whitespace-nowrap">{{ isset($kas->kredit) ? 'Rp ' . number_format($kas->kredit, 0, ',', '.') : '-' }}</td>
                        <td class="table-cell">{{ $kas->keterangan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="table-cell text-center py-4 font-medium text-gray-500">Tidak ada data Kas Harian yang tersedia.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <script>
        window.onload = function() {
            setTimeout(() => {
                triggerPrint('landscape'); 
            }, 500);
        };

        function triggerPrint(orientation) {
            let styleId = 'dynamic-print-style';
            let styleElement = document.getElementById(styleId);
            let docArea = document.getElementById('documentArea');

            if (!styleElement) {
                styleElement = document.createElement('style');
                styleElement.id = styleId;
                document.head.appendChild(styleElement);
            }

            if (orientation === 'landscape') {
                styleElement.innerHTML = `
                    @page { size: A4 landscape; margin: 10mm; }
                `;
                docArea.style.width = '297mm';
                docArea.style.minHeight = '210mm';
            } else {
                styleElement.innerHTML = `
                    @page { size: A4 portrait; margin: 10mm; }
                `;
                docArea.style.width = '210mm';
                docArea.style.minHeight = '297mm';
            }

            setTimeout(() => {
                window.print();
            }, 100);
        }
    </script>
</body>
</html>