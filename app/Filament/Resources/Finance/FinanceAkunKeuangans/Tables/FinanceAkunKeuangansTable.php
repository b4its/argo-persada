<?php

namespace App\Filament\Resources\Finance\FinanceAkunKeuangans\Tables;

use App\Models\AkunKeuangan;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FinanceAkunKeuangansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                AkunKeuangan::query()
                    ->orderBy('created_at', 'desc')
            )
            ->columns([
                TextColumn::make('kode')
                    ->label('Kode Akun')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nama Akun')
                    ->searchable()
                    ->sortable(),
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
                DeleteAction::make()
                    ->button()
                    ->color('danger') // default abu-abu (tidak merah)
                    ->requiresConfirmation() // pastikan tampil popup konfirmasi
                    ->modalHeading('Konfirmasi Hapus')
                    ->modalDescription('apakah yakin ingin menghapus data ini?')
                    ->modalSubmitActionLabel('Ya, Hapus'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
