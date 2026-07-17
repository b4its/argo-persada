<?php
namespace App\Filament\Tables\Actions;

use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\RawJs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class DetailPesananViewAction extends ViewAction
{
    // Nama default saat action ini dipanggil
    public static function getDefaultName(): ?string
    {
        return 'view_detail_pesanan';
    }

    // Pindahkan semua konfigurasi Anda ke dalam method setUp()
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('View Detail')
            ->modalHeading('Detail Pemesanan')
            ->form([
                DatePicker::make('tanggal_pemesanan')
                    ->label('Tanggal PO')
                    ->native(false)
                    ->disabled(),

                DatePicker::make('tanggal_terbit_surat_jalan')
                    ->label('Tanggal DO (Surat Jalan)')
                    ->native(false)
                    ->disabled(),

                Placeholder::make('durasi_po_do')
                    ->label('Durasi PO ke DO')
                    ->content(fn ($record): \Illuminate\Support\HtmlString => new \Illuminate\Support\HtmlString(
                        $record->created_at && $record->tanggal_terbit_surat_jalan
                            ? (function () use ($record) {
                                $totalMinutes = abs((int) $record->created_at->diffInMinutes(\Carbon\Carbon::parse($record->tanggal_terbit_surat_jalan)));

                                if ($totalMinutes >= 1440) {
                                    $days = (int) ($totalMinutes / 1440);
                                    $color = $days > 30 ? 'danger' : ($days > 14 ? 'warning' : 'success');
                                    $text = $days . ' hari';
                                } else {
                                    $hours = (int) ($totalMinutes / 60);
                                    $mins = $totalMinutes % 60;
                                    $text = $hours . ' jam' . ($mins ? ' ' . $mins . ' menit' : '');
                                    $color = 'info';
                                }

                                return '<span class="fi-badge flex items-center justify-center gap-x-1 rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset bg-' . $color . '-500/10 text-' . $color . '-700 ring-' . $color . '-700/10">' . e($text) . '</span>';
                            })()
                            : '<span class="text-sm text-gray-400">Pesanan belum sampai di Logistik</span>'
                    )),

                // 1. Ubah name menjadi nama field kustom (bukan dot notation)
                TextInput::make('company_internal_name')
                    ->label('Perusahaan Internal')
                    ->disabled(),
                
                TextInput::make('group_name')
                    ->label('Group')
                    ->disabled(),

                TextInput::make('company_name')
                    ->label('Company')
                    ->disabled(),

                Textarea::make('address')
                    ->label('Alamat Pengiriman')
                    ->disabled(),

                Repeater::make('dokumen_penagihan')
                    ->label('Detail Pesanan, dan Tagihan')
                    ->schema([
                        ### Input Nomor Dokumen (TextInput)
                        TextInput::make('no_requisition')
                            ->label('No Requisition')
                            ->placeholder('-')
                            ->prefixIcon('heroicon-m-document-text')
                            ->prefixIconColor('gray'),

                        TextInput::make('no_invoice')
                            ->label('No Invoice')
                            ->placeholder('-')
                            ->prefixIcon('heroicon-m-hashtag')
                            ->prefixIconColor('danger'), // Warna merah tipis agar terlihat kontras sebagai dokumen penting

                        TextInput::make('no_delivery_order')
                            ->label('No Delivery Order')
                            ->placeholder('-')
                            ->prefixIcon('heroicon-m-truck')
                            ->prefixIconColor('info'),

                        ### Input Tanggal (DatePicker)
                        DatePicker::make('tanggal_rilis_dana')
                            ->label('Tanggal Rilis Dana')
                            ->native(false)
                            ->displayFormat('d/m/Y') // Format tampilan yang lebih familiar di Indonesia
                            ->prefixIcon('heroicon-m-banknotes')
                            ->placeholder('Belum ditentukan'),

                        DatePicker::make('tanggal_terbit_invoice')
                            ->label('Tanggal Terbit Invoice')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->prefixIcon('heroicon-m-calendar-days')
                            ->placeholder('Belum ditentukan'),

                        DatePicker::make('tanggal_jatuh_tempo')
                            ->label('Tanggal Jatuh Tempo')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->prefixIcon('heroicon-m-clock')
                            ->prefixIconColor('warning') // Warna kuning sebagai tanda peringatan tenggat waktu
                            ->placeholder('Belum ditentukan'),

                        DatePicker::make('tanggal_lunas')
                            ->label('Tanggal Lunas')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->prefixIcon('heroicon-m-check-circle')
                            ->prefixIconColor('success')
                            ->placeholder('Belum ditentukan'),

                        Placeholder::make("metode_pembayaran_lunas")
                            ->label("Metode Pembayaran Lunas")
                            ->content(fn ($record): HtmlString => match ($record?->metode_pembayaran_lunas) {
                                1 => new HtmlString('<span class="fi-badge flex items-center justify-center gap-x-1 rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset bg-success-500/10 text-success-700 ring-success-700/10"><x-heroicon-m-check-badge class="w-4 h-4"/> Tunai</span>'),
                                2 => new HtmlString('<span class="fi-badge flex items-center justify-center gap-x-1 rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset bg-info-500/10 text-info-700 ring-info-700/10"><x-heroicon-m-credit-card class="w-4 h-4"/> Kredit</span>'),
                                3 => new HtmlString('<span class="fi-badge flex items-center justify-center gap-x-1 rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset bg-info-500/10 text-info-700 ring-info-700/10"><x-heroicon-m-credit-card class="w-4 h-4"/> Debit</span>'),
                                default => new HtmlString('<span class="fi-badge flex items-center justify-center gap-x-1 rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset bg-gray-500/10 text-gray-700 ring-gray-700/10">Belum Ditentukan</span>'),
                            }),
                        Placeholder::make("bank_lunas")
                            ->label("Bank / Rekening Lunas")
                            ->content(fn ($record): HtmlString => $record?->nama_bank_lunas
                                ? new HtmlString('<span class="text-sm">' . e($record->nama_bank_lunas) . ' - ' . e($record->no_rekening_lunas) . '</span>')
                                : new HtmlString('<span class="text-sm text-gray-400">-</span>')
                            ),
                    ])
                    ->columns(3)
                    ->disabled()
                    ->addable(false)
                    ->deletable(false)
                    ->reorderable(false)
                    ->collapsible()
                    ->collapsed()
                    ->itemLabel(fn (array $state): ?string => 
                        $state['no_invoice'] ? "Invoice: {$state['no_invoice']}" : "Detail Pesanan"
                    ),

                Repeater::make('list_barang')
                    ->label('Daftar Barang')
                    ->schema([
                        TextInput::make('item_name')->label('Nama Barang'),
                        TextInput::make('kode')->label('Kode Barang'),
                        TextInput::make('quantity')->numeric()->label('Qty'),
                        TextInput::make('satuan')->label('Satuan'),
                        TextInput::make('modal')
                            ->label('Harga Beli (Modal)*')
                            ->prefix('Rp')
                            ->numeric()
                            ->default(0)
                            ->required()
                            // 3. Gunakan mask sederhana jika ingin tetap ada pemisah ribuan saat input
                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)')),

                        TextInput::make('po')
                            ->label('Harga Jual (PO)*')
                            ->prefix('Rp')
                            ->numeric()
                            ->default(0)
                            ->required()
                            // 3. Gunakan mask sederhana jika ingin tetap ada pemisah ribuan saat input
                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)')),
                        TextInput::make('sub_total')
                            ->label('Sub Total')
                            ->prefix('Rp')
                            ->numeric()
                            ->default(0)
                            ->required()
                            // 3. Gunakan mask sederhana jika ingin tetap ada pemisah ribuan saat input
                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)')), 
                        TextInput::make('supplier_name')->label('Supplier')->columnSpanFull(),
                        Textarea::make('keterangan')->label('Keterangan')->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->disabled()
                    ->addable(false)
                    ->deletable(false)
                    ->reorderable(false)
                    ->collapsible()
                    ->collapsed()
                    ->itemLabel(fn (array $state): ?string => 
                        ($state['item_name'] ?? '') . (isset($state['satuan']) ? " - {$state['satuan']}" : "")
                    ),
            ])
            ->mutateRecordDataUsing(function (array $data, Model $record): array {
                // 2. Tambahkan loadMissing untuk relasi companyInternal agar tidak terkena N+1 query issue
                $record->loadMissing(['keranjang.queueKeranjang', 'companyInternal']);

                // 3. Masukkan data nama perusahaan ke state form
                $data['company_internal_name'] = $record->companyInternal?->name ?? '-';

                if ($record->keranjang && $record->keranjang->queueKeranjang) {
                    $data['list_barang'] = $record->keranjang->queueKeranjang->map(function ($item) {
                        return [
                            'item_name'     => $item->item_name,
                            'kode'      => $item->kode,
                            'quantity'      => $item->quantity,
                            'satuan'        => $item->satuan,
                            'modal'         => $item->modal,
                            'po'            => $item->po,
                            'sub_total'     => $item->sub_total,
                            'supplier_name' => $item->supplier_name,
                            'keterangan'    => $item->keterangan,
                        ];
                    })->toArray();
                }

                $data['dokumen_penagihan'] = [
                    [
                        'no_requisition'         => $record->no_requisition,
                        'no_invoice'             => $record->no_invoice,
                        'no_delivery_order'      => $record->no_delivery_order,
                        'tanggal_rilis_dana'     => $record->tanggal_rilis_dana,
                        'tanggal_terbit_invoice' => $record->tanggal_terbit_invoice,
                        'tanggal_jatuh_tempo'    => $record->tanggal_jatuh_tempo,
                        'tanggal_lunas'          => $record->tanggal_lunas,
                    ]
                ];

                $data['tanggal_pemesanan'] = $record->created_at;
                $data['tanggal_terbit_surat_jalan'] = $record->tanggal_terbit_surat_jalan;

                return $data;
            });
    }
}