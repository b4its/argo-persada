<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Invoice - PT Andalan Agro Persada</title>
    <style>
      body {
        font-family: Arial, sans-serif;
        font-size: 12px;
        color: #000;
        margin: 0;
      }

      main {
        width: 100%;
        max-width: 768px;
        padding-block: 24px;
        margin-inline: auto;
      }

      header {
        display: flex;
        align-items: stretch;
        gap: 16px;
        margin-bottom: 16px;
      }

      header img {
        aspect-ratio: 1 / 1;
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
      }

      table.invoice {
        width: 100%;
        border-collapse: collapse;
      }

      .terms-list {
        margin: 0;
        padding: 0;
        list-style: none;
      }

      .terms-list li {
        margin-bottom: 3px;
      }

      .bank-indent {
        padding-left: 14px;
      }

      /* Gaya untuk Tombol Aksi */
      .action-buttons {
        max-width: 768px;
        margin: 20px auto;
        display: flex;
        gap: 10px;
        justify-content: flex-end;
      }

      .btn {
        padding: 8px 16px;
        cursor: pointer;
        border: none;
        border-radius: 4px;
        font-weight: bold;
        text-decoration: none;
        color: white;
        font-size: 14px;
      }

      .btn-print { background-color: #28a745; }
      .btn-back { background-color: #6c757d; }

      /* KONFIGURASI PRINT TENGAH */
      @media print {
        /* Sembunyikan tombol */
        .action-buttons {
          display: none;
        }

        /* Memposisikan body agar konten di dalamnya center secara vertikal & horizontal */
        body {
          display: flex;
          justify-content: center; /* Center Horizontal */
          margin-top: 2em;
          min-height: 100vh;       /* Gunakan seluruh tinggi kertas */
        }

        main {
          margin: 0;
          padding: 0;
          width: 90%;             /* Pastikan tidak terpotong margin printer */
        }

        /* Menghilangkan header/footer default browser (opsional) */
        @page {
          margin: 0.5cm;
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

    <div class="action-buttons">
      <button onclick="window.history.back()" class="btn btn-back">Kembali</button>
      <button onclick="window.print()" class="btn btn-print">Cetak Invoice</button>
    </div>

    <main>
      <header>
          @php
              // Gunakan variabel untuk menampung path gambar
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


      <table border="1" cellpadding="4" cellspacing="0" class="invoice" style="border-color: black">
        <tr>
          <td colspan="2" style="vertical-align: top">
            <p style="font-weight: bold; font-size: 14px; text-decoration: underline; margin: 0 0 4px 0">INVOICE</p>
            <table cellpadding="2" cellspacing="0" style="border: none; width: 100%">
              <tr style="font-weight: bold">
                <td style="width: 80px">Date</td>
                <td style="width: 10px">:</td>
                <td>{{ $latestPesanan->tanggal_terbit_invoice ? \Carbon\Carbon::parse($latestPesanan->tanggal_terbit_invoice)->format('d-m-Y') : \Carbon\Carbon::now()->format('d-m-Y') }}</td>
              </tr>
              <tr style="font-weight: bold">
                <td>No</td>
                <td>:</td>
                <td>{{ $latestPesanan->no_invoice ?? $latestPesanan->code ?? '-' }}</td>
              </tr>
              <tr style="font-weight: bold">
                <td>PO No</td>
                <td>:</td>
                <td>{{ $latestPesanan->no_po ?? '-' }}</td>
              </tr>
              <tr style="font-weight: bold">
                <td>Jatuh Tempo</td>
                <td>:</td>
                <td>{{ $latestPesanan->tanggal_jatuh_tempo ? \Carbon\Carbon::parse($latestPesanan->tanggal_jatuh_tempo)->format('d-m-Y') : '-' }}</td>
              </tr>
            </table>
          </td>

          <td colspan="4" style="vertical-align: top; font-weight: bold">
            <p style="margin: 0">Kepada Yth.</p>
            <p style="margin: 0">{{ $latestPesanan->company_name ?? $latestPesanan->group_name }}</p>
            <p style="margin: 0; font-weight: normal;">{{ $latestPesanan->address ?? '' }}</p>
          </td>
        </tr>

        <tr style="text-align: center">
          <th style="width: 4%">No</th>
          <th style="width: 42%">Nama Barang</th>
          <th style="width: 9%">Jumlah</th>
          <th style="width: 9%">Satuan</th>
          <th style="width: 18%">Harga Satuan (Rp.)</th>
          <th style="width: 18%">Sub Total (Rp.)</th>
        </tr>

        @forelse($items as $index => $item)
        <tr style="height: 22px">
          <td style="text-align: center">{{ $index + 1 }}</td>
          <td>{{ $item->item_name }}</td>
          <td style="text-align: center">{{ $item->quantity }}</td>
          <td style="text-align: center">{{ $item->satuan }}</td>
          <td style="text-align: right">{{ number_format($item->po, 0, ',', '.') }}</td>
          <td style="text-align: right">{{ number_format($item->sub_total, 0, ',', '.') }}</td>
        </tr>
        @empty
        <tr style="height: 22px">
          <td colspan="6" style="text-align: center; font-style: italic;">Tidak ada data barang dalam pesanan ini.</td>
        </tr>
        @endforelse

        <tr>
          <td colspan="2" rowspan="3"></td>
          <td colspan="3" style="text-align: center; font-weight: bold">Total</td>
          <td style="text-align: right">{{ number_format($latestPesanan->keranjang->sub_total ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
          <td colspan="3" style="text-align: center; font-weight: bold">PPN 11 %</td>
          <td style="text-align: right">{{ number_format($latestPesanan->ppn ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
          <td colspan="3" style="text-align: center; font-weight: bold">Total Pembayaran</td>
          <td style="text-align: right; font-weight: bold">{{ number_format($latestPesanan->total_harga ?? 0, 0, ',', '.') }}</td>
        </tr>

        <tr>
          <td colspan="6" style="padding-block: 20px">
              <strong>Terbilang:</strong> <em>{{ terbilang($latestPesanan->total_harga ?? 0) }}</em>
          </td>
        </tr>

        <tr>
          <td colspan="2" style="vertical-align: top">
            <p style="font-weight: bold; margin: 0 0 4px 0">Term And Conditions :</p>
            <ul class="terms-list">
              <li>- Barang yang sudah dibeli tidak dapat dikembalikan atau ditukar</li>
              <li>- Tidak menerima pembayaran tunai, pembayaran dilakukan dengan transfer ke rekening berikut :</li>
              <li class="bank-indent">Bank Mandiri</li>
              <li class="bank-indent">Cabang A. Yani, Samarinda</li>
              <li class="bank-indent">No Rek : xxxx</li>
              <li>- Pembayaran dengan Giro/Cek dianggap sah apabila sudah diterima di rekening kami</li>
            </ul>
          </td>

          <td colspan="4">
            <div style="height: 80%; display: flex; flex-direction: column; justify-content: space-between; padding-top: 10px;">
              <div style="text-align: center;">
                <p style="margin: 0">Hormat Kami,</p>
                <p style="margin: 0">{{ $latestPesanan->companyInternal->name ?? "PT Andalan Argo Persada" }}</p>
              </div>
              <p style="margin: 0; text-align: center; margin-top: 60px;">( {{ Auth::user()->name ?? 'Nama Pembuat' }} )</p>
            </div>
          </td>
        </tr>
      </table>
    </main>
  </body>
  <script>
    print()
  </script>
</html>