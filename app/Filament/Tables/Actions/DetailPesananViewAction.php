<?php
namespace App\Filament\Tables\Actions;

use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Support\RawJs;
use Illuminate\Database\Eloquent\Model;

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
                    ->label('Tanggal Pemesanan')
                    ->native(false)
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
                        TextInput::make('no_requisition')->label('No Requisition')->placeholder('-'),
                        TextInput::make('no_invoice')->label('No Invoice')->placeholder('-'),
                        TextInput::make('no_delivery_order')->label('No Delivery Order')->placeholder('-'),
                        DatePicker::make('tanggal_rilis_dana')->label('Tanggal Rilis Dana')->native(false)->placeholder('belum ditentukan'),
                        DatePicker::make('tanggal_terbit_invoice')->label('Tanggal Terbit Invoice')->native(false)->placeholder('belum ditentukan'),
                        DatePicker::make('tanggal_jatuh_tempo')->label('Tanggal Jatuh Tempo')->native(false)->placeholder('belum ditentukan'),
                        DatePicker::make('tanggal_lunas')->label('Tanggal Lunas')->native(false)->placeholder('belum ditentukan'),
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
                        TextInput::make('quantity')->numeric()->label('Qty'),
                        TextInput::make('satuan')->label('Satuan'),
                        TextInput::make('modal')
                            ->label('Harga Beli (Modal)*')
                            ->prefix('Rp') 
                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)')) // Format angka Indonesia
                            ->stripCharacters('.') // Buang titik sebelum masuk ke database
                            ->numeric()
                            ->default(0)
                            ->required(),

                        TextInput::make('po')
                            ->label('Harga Jual (PO)*')
                            ->prefix('Rp') 
                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)')) // Format angka Indonesia
                            ->stripCharacters('.') // Buang titik sebelum masuk ke database
                            ->numeric()
                            ->default(0)
                            ->required(),
                        TextInput::make('sub_total')
                            ->label('Sub Total')
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)')) // Format angka Indonesia
                            ->stripCharacters('.') // Buang titik sebelum masuk ke database
                            ->numeric()
                            ->default(0)
                            ->required(),   
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
                $record->load(['keranjang.queueKeranjang']);

                if ($record->keranjang && $record->keranjang->queueKeranjang) {
                    $data['list_barang'] = $record->keranjang->queueKeranjang->map(function ($item) {
                        return [
                            'item_name'     => $item->item_name,
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

                return $data;
            });
    }
}