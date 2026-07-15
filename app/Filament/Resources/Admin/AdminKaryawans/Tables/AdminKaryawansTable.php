<?php

namespace App\Filament\Resources\Admin\AdminKaryawans\Tables;

use App\Models\Pesanan;
use App\Models\Task;
use App\Models\TaskActivity;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AdminKaryawansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    // Pastikan nama tabel 'users' sesuai migrasi
                    ->selectRaw('users.*, ROW_NUMBER() OVER (ORDER BY created_at desc) as row_num')
                    ->where('role', '!=', 'admin') // Exclude admin from the list
                    ->orderBy('created_at', 'desc')
            )
            ->columns([
                //
                TextColumn::make('row_num')
                    ->label('No')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('role')
                    ->label('Role')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Filter Role')
                    ->options([
                        'marketing' => 'Marketing',
                        'finance' => 'Finance',
                        'logistik' => 'Logistik',
                        'admin' => 'Admin',
                        'superadmin' => 'Superadmin',
                        'guest' => 'Guest',
                    ])
                    ->placeholder('Semua Role'),
            ])
            ->recordActions([
                Action::make('showActivity')
                    ->label('Aktivitas')
                    ->icon('heroicon-m-clipboard-document-list')
                    ->color('info')
                    ->modalHeading(fn ($record) => 'Aktivitas: ' . $record->name)
                    ->modalWidth('4xl')
                    ->modalContent(fn ($record) => view('filament.resources.admin-karyawans.user-activity-modal', ['user' => $record])),
                EditAction::make(),
                DeleteAction::make()
                    ->button()
                    ->color('danger')
                    ->requiresConfirmation()
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
