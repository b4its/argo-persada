<?php

namespace App\Filament\Resources\Finance\FinanceKasHarians\Tables;

use App\Models\KasHarian;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

// Import yang Anda wajibkan (Sesuai arsitektur Filament 5.x / Laravel 13 Anda)
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Builder;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;

// Import tambahan murni untuk kebutuhan kelengkapan Infolist & Table
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FinanceKasHariansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                fn () => KasHarian::with(['companyInternal', 'akunKeuangan', 'user', 'pesanan.keranjang.queueKeranjang'])
                    ->orderBy('created_at', 'desc')
            )
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d/m/Y H:i')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('akunKeuangan.kode')
                    ->label('Kode Akun')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('companyInternal.singkatan')
                    ->label('PT')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('PIC')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('pesanan.no_requisition')
                    ->label('PR/PO')
                    ->default('-')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('debet')
                    ->label('Debet')
                    ->numeric(2)
                    ->color('success')
                    ->sortable(),

                TextColumn::make('kredit')
                    ->label('Kredit')
                    ->numeric(2)
                    ->color('danger')
                    ->sortable(),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                
                TextColumn::make('kategori')
                    ->label('Kategori')
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        1 => 'Penjualan',
                        2 => 'Piutang',
                        3 => 'Biaya Umum dan Administrasi Kantor',
                        4 => 'Biaya Lain Lain',
                        default => 'Unknown',
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        // Logika ini memetakan kata kunci pencarian ke angka ID di database
                        $categories = [
                            1 => 'penjualan',
                            2 => 'piutang',
                            3 => 'biaya umum dan administrasi kantor',
                            4 => 'biaya lain lain',
                        ];

                        return $query->where(function ($q) use ($search, $categories) {
                            foreach ($categories as $id => $name) {
                                if (str_contains(strtolower($name), strtolower($search))) {
                                    $q->orWhere('kategori', $id);
                                }
                            }
                        });
                    })
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                ViewAction::make()
                    ->label('Detail')
                    ->icon('heroicon-m-eye')
                    ->modalHeading('Audit Detail Kas Harian & Dokumen Pesanan')
                    ->modalWidth('7xl') // Diperlebar menjadi 7xl untuk menampung tabel rincian keranjang
                    ->visible(fn ($record) => $record && $record->pesanan_id !== null)
                    ->infolist([
                        
                        // ROW 1: MUTASI KAS & ENTITAS
                        Section::make('Informasi Transaksi Kas')
                            ->icon('heroicon-m-banknotes')
                            ->columns(4)
                            ->schema([
                                TextEntry::make('companyInternal.name')->label('PT Internal')->weight('bold'),
                                TextEntry::make('akunKeuangan.name')->label('Akun Keuangan'),
                                TextEntry::make('user.name')->label('PIC (User)'),
                                TextEntry::make('toko')->label('Toko/Vendor')->default('-'),
                                
                                TextEntry::make('saldo_awal')->label('Saldo Awal')->numeric(2)->prefix('Rp ')->color('gray'),
                                TextEntry::make('debet')->label('Debet (Masuk)')->numeric(2)->prefix('Rp ')->color('success'),
                                TextEntry::make('kredit')->label('Kredit (Keluar)')->numeric(2)->prefix('Rp ')->color('danger'),
                                TextEntry::make('saldo_akhir')->label('Saldo Akhir')->numeric(2)->prefix('Rp ')->weight('bold')->size('lg'),
                            ]),

                        // ROW 2: DETAIL PESANAN UTAMA
                        Section::make('Dokumen Pesanan Terkait')
                            ->icon('heroicon-m-document-duplicate')
                            ->columns(3)
                            ->schema([
                                Fieldset::make('Identitas Pesanan')->schema([
                                    TextEntry::make('pesanan.code')->label('Kode Pesanan')->fontFamily('mono')->copyable(),
                                    TextEntry::make('pesanan.group_name')->label('Group Name')->default('-'),
                                    TextEntry::make('pesanan.company_name')->label('Company (External)')->default('-'),
                                    TextEntry::make('pesanan.address')->label('Alamat Kirim')->columnSpanFull()->default('-'),
                                ])->columns(3)->columnSpanFull(),

                                Fieldset::make('Nomor Dokumen')->schema([
                                    TextEntry::make('pesanan.no_requisition')->label('No. Requisition')->fontFamily('mono')->default('-'),
                                    TextEntry::make('pesanan.no_po')->label('No. PO')->fontFamily('mono')->default('-'),
                                    TextEntry::make('pesanan.no_delivery_order')->label('No. DO')->fontFamily('mono')->default('-'),
                                    TextEntry::make('pesanan.no_invoice')->label('No. Invoice')->fontFamily('mono')->default('-'),
                                ])->columns(4)->columnSpanFull(),

                                Fieldset::make('Jadwal & Waktu')->schema([
                                    TextEntry::make('pesanan.tanggal_rilis_dana')->label('Rilis Dana')->date('d M Y')->default('-'),
                                    TextEntry::make('pesanan.tanggal_terbit_surat_jalan')->label('Surat Jalan')->date('d M Y')->default('-'),
                                    TextEntry::make('pesanan.tanggal_surat_kembali')->label('Surat Kembali')->date('d M Y')->default('-'),
                                    TextEntry::make('pesanan.tanggal_terbit_invoice')->label('Terbit Invoice')->date('d M Y')->default('-'),
                                    TextEntry::make('pesanan.tanggal_jatuh_tempo')->label('Jatuh Tempo')->date('d M Y')->color('danger')->default('-'),
                                    TextEntry::make('pesanan.tanggal_lunas')->label('Lunas Pada')->date('d M Y')->color('success')->default('-'),
                                ])->columns(3)->columnSpanFull(),

                                Fieldset::make('Status')->schema([
                                    TextEntry::make('pesanan.status_pesanan')
                                        ->label('Status Pesanan')
                                        ->badge()
                                        ->color(fn (string $state): string => match ($state) {
                                            '0' => 'slate',      // dibuat
                                            '1' => 'sunshine',   // pending
                                            '2' => 'crimson',    // perlu rilis dana (urgent)
                                            '3' => 'ocean',      // perlu cetak invoice
                                            '4' => 'lavender',   // perlu penagihan
                                            '5' => 'emerald',    // ditandai lunas
                                            '6' => 'royal',      // cetak surat jalan
                                            '7' => 'info',       // tandai selesai dikirim
                                            '8' => 'success',    // selesai
                                            default => 'gray',
                                        })
                                        ->formatStateUsing(fn (string $state): string => match ($state) {
                                            '0' => 'Dibuat',
                                            '1' => 'Pending',
                                            '2' => 'Perlu Rilis Dana',
                                            '3' => 'Perlu Cetak Invoice',
                                            '4' => 'Perlu Penagihan',
                                            '5' => 'Ditandai Lunas',
                                            '6' => 'Cetak Surat Jalan',
                                            '7' => 'Selesai Dikirim',
                                            '8' => 'Selesai',
                                            default => 'Unknown',
                                        }),

                                    TextEntry::make('pesanan.status_perilisan_dana')
                                        ->label('Status Rilis Dana')
                                        ->badge()
                                        ->color(fn (string $state): string => match ($state) {
                                            '0' => 'slate',    // dibuat
                                            '1' => 'sunshine', // pending
                                            '2' => 'danger',   // dibatalkan
                                            '3' => 'emerald',  // approved
                                            default => 'gray',
                                        })
                                        ->formatStateUsing(fn (string $state): string => match ($state) {
                                            '0' => 'Dibuat',
                                            '1' => 'Pending',
                                            '2' => 'Dibatalkan',
                                            '3' => 'Approved',
                                            default => 'Unknown',
                                        }),

                                ])->columns(4)->columnSpanFull(),
                                
                                Fieldset::make('Total Pesanan (Termasuk Keranjang)')->schema([
                                    TextEntry::make('pesanan.ppn')->label('PPN')->numeric(2)->prefix('Rp '),
                                    TextEntry::make('pesanan.total_harga')->label('Grand Total Pesanan')->numeric(2)->prefix('Rp ')->weight('bold')->size('lg')->color('primary'),
                                    TextEntry::make('pesanan.keranjang.sub_total')->label('Sub Total Keranjang')->numeric(2)->prefix('Rp '),
                                ])->columns(3)->columnSpanFull(),
                            ]),

                        // ROW 3: RINCIAN ITEM (QUEUE KERANJANG)
                        Section::make('Rincian Item (Daftar Pembelanjaan)')
                            ->icon('heroicon-m-shopping-cart')
                            ->schema([
                                RepeatableEntry::make('pesanan.keranjang.queueKeranjang')
                                    ->hiddenLabel() // Sembunyikan label karena sudah diwakili Section
                                    ->columns(5) // Buat layout Grid per baris item
                                    ->schema([
                                        TextEntry::make('item_name')
                                            ->label('Nama Item / Barang')
                                            ->weight('bold'),
                                            
                                        TextEntry::make('supplier_name')
                                            ->label('Supplier')
                                            ->default('-'),

                                        TextEntry::make('quantity')
                                            ->label('Qty')
                                            ->formatStateUsing(fn ($state, $record) => $state . ' ' . $record->satuan),

                                        TextEntry::make('modal')
                                            ->label('Harga Satuan')
                                            ->numeric(2)
                                            ->prefix('Rp '),

                                        TextEntry::make('sub_total')
                                            ->label('Total (Qty x Harga)')
                                            ->numeric(2)
                                            ->prefix('Rp ')
                                            ->color('success'),
                                            
                                        TextEntry::make('keterangan')
                                            ->label('Catatan Item')
                                            ->columnSpanFull()
                                            ->default('-')
                                            ->color('gray'),
                                    ])
                            ])
                            // Sembunyikan section ini jika transaksi tidak punya relasi pesanan/keranjang
                            ->visible(fn ($record) => $record->pesanan && $record->pesanan->keranjang),
                    ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}