<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MONITORING PO MASUK 2026</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    font-family: Arial, sans-serif;
    font-size: 11px;
    background: #f0f0f0;
    padding: 20px;
  }

  .screen-only {
    display: flex;
    gap: 10px;
    align-items: center;
    margin-bottom: 16px;
    flex-wrap: wrap;
  }

  .btn {
    padding: 8px 18px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 13px;
    font-weight: bold;
    transition: background 0.15s;
  }
  .btn-back {
    background: #555;
    color: #fff;
  }
  .btn-back:hover { background: #333; }
  .btn-print {
    background: #1a6bb5;
    color: #fff;
  }
  .btn-print:hover { background: #145591; }

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

  .page-wrapper {
    background: #fff;
    padding: 18px 20px 24px 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.15);
    max-width: 100%;
    overflow-x: auto;
  }

  .legend-area {
    margin-bottom: 14px;
  }
  .legend-row {
    display: flex;
    align-items: center;
    margin-bottom: 3px;
    font-size: 11px;
    font-weight: bold;
  }
  .legend-color {
    display: inline-block;
    width: 22px;
    height: 14px;
    margin-right: 8px;
    border: 1px solid #ccc;
  }
  .lg-biru   { background: #9DC3E6; }
  .lg-hijau  { background: #00FF99; }
  .lg-pink   { background: #FFB6C1; }
  .lg-orange { background: #FFC000; }

  /* Table */
  .po-table {
    border-collapse: collapse;
    width: 100%;
    min-width: 1100px;
    font-size: 10px;
  }
  .po-table th, .po-table td {
    border: 1px solid #000;
    padding: 3px 4px;
    text-align: center;
    vertical-align: middle;
    white-space: nowrap;
  }
  .po-table td {
    height: 22px;
  }

  /* Header colors */
  .h-blue   { background: #9DC3E6; }
  .h-green  { background: #00FF99; }
  .h-orange { background: #FFC000; }
  .h-cyan   { background: #00B0F0; }
  .h-light  { background: #E2EFDA; }

  .po-table th {
    font-weight: bold;
    font-size: 10px;
  }

  /* Data rows alternating */
  .po-table tbody tr:nth-child(even) td {
    background: #fafafa;
  }
  .po-table tbody tr:hover td {
    background: #EAF4FB;
  }

  /* Row numbering col narrow */
  .col-no { width: 28px; }
  .col-tgl { width: 62px; }
  .col-group { width: 70px; }
  .col-company { width: 70px; }
  .col-nopo { width: 68px; }
  .col-nama { width: 170px; white-space: normal; }
  .col-qty { width: 34px; }
  .col-stn { width: 44px; }
  .col-hpo { width: 80px; }
  .col-total { width: 80px; }
  .col-hmodal { width: 80px; }
  .col-total2 { width: 80px; }
  .col-suplier { width: 70px; }
  .col-nomordb { width: 70px; }
  .col-tglrilis { width: 70px; }
  .col-nomordo { width: 70px; }
  .col-tgldo { width: 70px; }
  .col-tglkembali { width: 70px; }
  .col-tglinvoice { width: 60px; }
  .col-nominvoice { width: 60px; }
  .col-tgllunas { width: 60px; }
  .col-ket { width: 80px; }

  /* ===== PRINT STYLES ===== */
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
      padding: 8px 10px !important;
      overflow: visible !important;
    }
    .po-table {
      font-size: 7.5pt !important;
    }
    .po-table th, .po-table td {
      padding: 2px 3px !important;
    }
    /* Colors must print */
    .h-blue   { -webkit-print-color-adjust: exact; print-color-adjust: exact; background: #9DC3E6 !important; }
    .h-green  { -webkit-print-color-adjust: exact; print-color-adjust: exact; background: #00FF99 !important; }
    .h-orange { -webkit-print-color-adjust: exact; print-color-adjust: exact; background: #FFC000 !important; }
    .h-cyan   { -webkit-print-color-adjust: exact; print-color-adjust: exact; background: #00B0F0 !important; }
    .h-light  { -webkit-print-color-adjust: exact; print-color-adjust: exact; background: #E2EFDA !important; }
    * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
  }
</style>
</head>
<body>

<div class="screen-only">
  <button class="btn btn-back" onclick="history.back()">&#8592; Kembali</button>
  <button class="btn btn-print" onclick="window.print()">&#128438; Print</button>
  <a href="{{ route('export.surat.pesanan', request()->all()) }}" class="btn" style="background: #217346; color: #fff; text-decoration: none;">&#128190; Export Excel</a>
  <label class="orientation-label" for="orientSelect">Orientasi:</label>
  <select class="orientation-select" id="orientSelect" onchange="setOrientation(this.value)">
    <option value="landscape" selected>Landscape (Horizontal)</option>
    <option value="portrait">Portrait (Vertikal)</option>
  </select>
  <span style="margin-left: auto; font-weight: bold; color: #333;">Welcome, {{ $username }}</span>
</div>

<style id="printOrientStyle">
  @page { size: A4 landscape; margin: 8mm 8mm 8mm 8mm; }
</style>

<div class="page-wrapper" id="pageWrapper">

  <div class="legend-area">
    <div class="legend-row"><span class="legend-color lg-biru"></span> BIRU &nbsp;&nbsp;&nbsp; MARKETING</div>
    <div class="legend-row"><span class="legend-color lg-hijau"></span> HIJAU &nbsp;&nbsp; INKA</div>
    <div class="legend-row"><span class="legend-color lg-pink"></span> PINK &nbsp;&nbsp;&nbsp; LISA</div>
    <div class="legend-row"><span class="legend-color lg-orange"></span> ORANGE &nbsp;RINA</div>
  </div>

  <table class="po-table" id="poTable">
    <thead>
      <tr>
        <th class="h-blue col-no" rowspan="2">No</th>
        <th class="h-blue col-tgl" style="border-bottom: 1px solid transparent">Tggl PO</th>
        <th class="h-blue col-group" rowspan="2">Group</th>
        <th class="h-blue col-company" rowspan="2">Company</th>
        <th class="h-blue col-nopo" rowspan="2">No PO</th>
        <th class="h-blue col-nama" rowspan="2">Nama Barang</th>
        <th class="h-blue col-qty" rowspan="2">Qty</th>
        <th class="h-blue col-stn" rowspan="2">STN</th>
        <th class="h-blue" colspan="2">H. PO</th>
        <th class="h-blue col-hmodal" rowspan="2">H. Modal</th>
        <th class="h-blue col-total2" rowspan="2">TOTAL</th>
        <th class="h-blue col-suplier" rowspan="2">Suplier</th>
        <th class="h-blue col-nomordb" rowspan="2">NOMOR DB/PO</th>
        <th class="h-green col-tglrilis">TANGGAL RILIS</th>
        <th class="h-orange col-nomordo" rowspan="2">NOMOR DO</th>
        <th class="h-orange col-tgldo" rowspan="2">Tanggal DO</th>
        <th class="lg-pink col-tglkembali" style="border-bottom: 1px solid transparent">TANGGAL</th>
        <th class="h-orange col-tglinvoice" style="border-bottom: 1px solid transparent">Tanggal</th>
        <th class="h-orange col-nominvoice" style="border-bottom: 1px solid transparent">Nomor</th>
        <th class="h-orange col-tgllunas" style="border-bottom: 1px solid transparent">Tanggal</th>
        <th class="h-cyan col-ket" rowspan="2">Ket</th>
      </tr>
      <tr>
        <th class="h-blue col-tgl">Masuk</th>
        <th class="h-blue col-hpo" style="min-width:55px;">H. BARANG</th>
        <th class="h-blue col-total">TOTAL</th>
        <th class="h-green">DANA</th>
        <th class="lg-pink">KEMBALI DO</th>
        <th class="h-orange">Invoice</th>
        <th class="h-orange">Invoice</th>
        <th class="h-orange">Lunas</th>
      </tr>
    </thead>
    <tbody id="tableBody">
    </tbody>
  </table>
</div>

@php
    $dataFromDB = [];
    foreach($pesananAll as $pesanan) {
        // Query item dari queue_keranjang menggunakan facade DB (Tidak butuh Model Update)
        $items = \Illuminate\Support\Facades\DB::table('queue_keranjang')
                    ->where('keranjang_id', $pesanan->keranjang_id)
                    ->get();

        if($items->count() > 0) {
            foreach($items as $item) {
                $dataFromDB[] = [
                    'pic' => '', 
                    'tgl_po' => $pesanan->created_at ? \Carbon\Carbon::parse($pesanan->created_at)->format('d/m/y') : '-',
                    'group' => $pesanan->group_name ?? '-',
                    'company' => $pesanan->company_name ?? '-',
                    'no_po' => $pesanan->code ?? '-',
                    'nama_barang' => $item->item_name ?? '-',
                    'qty' => $item->quantity ?? 0,
                    'stn' => $item->satuan ?? '-',
                    'h_po' => $item->po ?? 0,
                    'h_modal' => $item->modal ?? 0,
                    'suplier' => $item->supplier_name ?? '-',
                    'no_db' => $pesanan->no_po ?? '-',
                    'tgl_rilis' => $pesanan->tanggal_rilis_dana ? \Carbon\Carbon::parse($pesanan->tanggal_rilis_dana)->format('d/m/y') : '-',
                    'no_do' => $pesanan->no_delivery_order ?? '-',
                    'tgl_do' => $pesanan->tanggal_terbit_surat_jalan ? \Carbon\Carbon::parse($pesanan->tanggal_terbit_surat_jalan)->format('d/m/y') : '-',
                    'tgl_kembali' => $pesanan->tanggal_surat_kembali ? \Carbon\Carbon::parse($pesanan->tanggal_surat_kembali)->format('d/m/y') : '-',
                    'tgl_inv' => $pesanan->tanggal_terbit_invoice ? \Carbon\Carbon::parse($pesanan->tanggal_terbit_invoice)->format('d/m/y') : '-',
                    'no_inv' => $pesanan->no_invoice ?? '-',
                    'tgl_lunas' => $pesanan->tanggal_lunas ? \Carbon\Carbon::parse($pesanan->tanggal_lunas)->format('d/m/y') : '-',
                    'ket' => $item->keterangan ?? '-'
                ];
            }
        } else {
            // Fallback jika pesanan belum memiliki antrian item
            $dataFromDB[] = [
                'pic' => '',
                'tgl_po' => $pesanan->created_at ? \Carbon\Carbon::parse($pesanan->created_at)->format('d/m/y') : '-',
                'group' => $pesanan->group_name ?? '-',
                'company' => $pesanan->company_name ?? '-',
                'no_po' => $pesanan->code ?? '-',
                'nama_barang' => '-',
                'qty' => 0,
                'stn' => '-',
                'h_po' => 0,
                'h_modal' => 0,
                'suplier' => '-',
                'no_db' => $pesanan->no_po ?? '-',
                'tgl_rilis' => $pesanan->tanggal_rilis_dana ? \Carbon\Carbon::parse($pesanan->tanggal_rilis_dana)->format('d/m/y') : '-',
                'no_do' => $pesanan->no_delivery_order ?? '-',
                'tgl_do' => $pesanan->tanggal_terbit_surat_jalan ? \Carbon\Carbon::parse($pesanan->tanggal_terbit_surat_jalan)->format('d/m/y') : '-',
                'tgl_kembali' => $pesanan->tanggal_surat_kembali ? \Carbon\Carbon::parse($pesanan->tanggal_surat_kembali)->format('d/m/y') : '-',
                'tgl_inv' => $pesanan->tanggal_terbit_invoice ? \Carbon\Carbon::parse($pesanan->tanggal_terbit_invoice)->format('d/m/y') : '-',
                'no_inv' => $pesanan->no_invoice ?? '-',
                'tgl_lunas' => $pesanan->tanggal_lunas ? \Carbon\Carbon::parse($pesanan->tanggal_lunas)->format('d/m/y') : '-',
                'ket' => '-'
            ];
        }
    }
@endphp

<script>
  // Mengkonversi array PHP ke JSON untuk JavaScript (Sangat aman dari masalah XSS atau kutip)
  const dataPO = @json($dataFromDB);

  const formatRupiah = (angka) => {
    return angka ? Number(angka).toLocaleString('id-ID') : '0';
  };

  function buildRows(count) {
    const tbody = document.getElementById('tableBody');
    tbody.innerHTML = '';
    
    // Agar template tidak kosong, kita loop sebanyak jumlah data asli. 
    // Jika data kosong, loop akan ditangani dengan kondisi if dibawah atau bisa diset minimal baris.
    const loopCount = dataPO.length > 0 ? dataPO.length : count;

    for (let i = 0; i < loopCount; i++) {
      const tr = document.createElement('tr');
      const d = dataPO[i] || {};

      // Kalkulasi Total
      const total_po = (d.qty && d.h_po) ? d.qty * d.h_po : 0;
      const total_modal = (d.qty && d.h_modal) ? d.qty * d.h_modal : 0;

      let bgColorNo = '';

      // Struktur Data Per Baris (22 Kolom)
      const cellsData = [
        { val: i + 1, bg: bgColorNo },
        { val: d.tgl_po || '' },
        { val: d.group || '' },
        { val: d.company || '' },
        { val: d.no_po || '' },
        { val: d.nama_barang || '', align: 'left' },
        { val: d.qty || '' },
        { val: d.stn || '' },
        { val: formatRupiah(d.h_po), align: 'right' },
        { val: formatRupiah(total_po), align: 'right' },
        { val: formatRupiah(d.h_modal), align: 'right' },
        { val: formatRupiah(total_modal), align: 'right' },
        { val: d.suplier || '' },
        { val: d.no_db || '' },
        { val: d.tgl_rilis || '' },
        { val: d.no_do || '' },
        { val: d.tgl_do || '' },
        { val: d.tgl_kembali || '' },
        { val: d.tgl_inv || '' },
        { val: d.no_inv || '' },
        { val: d.tgl_lunas || '' },
        { val: d.ket || '' }
      ];

      cellsData.forEach(cell => {
        const td = document.createElement('td');
        td.style.height = '22px';
        
        if(cell.bg) td.style.background = cell.bg;
        if(cell.align) td.style.textAlign = cell.align;
        
        td.textContent = cell.val;
        tr.appendChild(td);
      });

      tbody.appendChild(tr);
    }
  }

  // Panggil fungsi (akan memproses seluruh data di DB atau fallback ke 30 blank baris jika tabel DB kosong)
  buildRows(30);

  function setOrientation(val) {
    const styleEl = document.getElementById('printOrientStyle');
    if (val === 'landscape') {
      styleEl.textContent = '@page { size: A4 landscape; margin: 8mm 8mm 8mm 8mm; }';
    } else {
      styleEl.textContent = '@page { size: A4 portrait; margin: 10mm 8mm 10mm 8mm; }';
    }
  }

  window.addEventListener('load', function () {
    setOrientation('landscape');
    window.print() 
    // Jika mau langsung print pas dibuka, uncomment baris bawah:
    // window.print();
  });
</script>
</body>
</html>