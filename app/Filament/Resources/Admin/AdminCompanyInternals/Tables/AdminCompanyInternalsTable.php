<?php

namespace App\Filament\Resources\Admin\AdminCompanyInternals\Tables;

use App\Models\CompanyInternal;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminCompanyInternalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                CompanyInternal::query()
                    // Pastikan nama tabel 'users' sesuai migrasi
                    ->selectRaw('company_internal.*, ROW_NUMBER() OVER (ORDER BY created_at desc) as row_num')
                    ->orderBy('created_at', 'desc')
            )
            ->columns([
                //
                TextColumn::make('row_num')
                    ->label('No')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Nama')
                    ->sortable(),

                TextColumn::make('singkatan')
                    ->label('Singkatan'),
                TextColumn::make('is_ppn')
                    ->label('PPN')
                    ->formatStateUsing(fn (string $state): string => $state === '1' ? 'Iya' : 'Tidak')

            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->using(function ($record, array $data): \Illuminate\Database\Eloquent\Model {
                        // 1. Cek apakah ada perubahan gambar atau singkatan
                        $oldSingkatan = Str::slug($record->singkatan, '_');
                        $newSingkatan = Str::slug($data['singkatan'], '_');
                        $oldImagePath = $record->gambar;
                        $newImagePath = $data['gambar'];

                        // 2. Jalankan update data ke database dahulu
                        $record->update($data);

                        // 3. Logika Pemindahan file dari 'temp' ke folder 'id' (Jika baru upload)
                        if ($newImagePath && str_contains($newImagePath, '/temp/')) {
                            $filename = basename($newImagePath);
                            $destinationPath = "media/company-internal/{$newSingkatan}/logo/{$record->id}/{$filename}";

                            if (Storage::disk('public_folder')->exists($newImagePath)) {
                                // Pindahkan file fisik
                                Storage::disk('public_folder')->move($newImagePath, $destinationPath);
                                
                                // Update path di database ke lokasi permanen
                                $record->update(['gambar' => $destinationPath]);
                                
                                // Hapus folder temp jika kosong
                                Storage::disk('public_folder')->deleteDirectory("media/company-internal/{$newSingkatan}/logo/temp");
                            }
                        } 
                        // 4. Logika jika folder berubah karena singkatan diedit (tapi gambar tetap)
                        elseif ($oldSingkatan !== $newSingkatan && $newImagePath && !str_contains($newImagePath, '/temp/')) {
                            $oldDir = "media/company-internal/{$oldSingkatan}/logo/{$record->id}";
                            $newDir = "media/company-internal/{$newSingkatan}/logo/{$record->id}";
                            
                            if (Storage::disk('public_folder')->exists($oldDir)) {
                                Storage::disk('public_folder')->move($oldDir, $newDir);
                                $newDbPath = str_replace($oldDir, $newDir, $newImagePath);
                                $record->update(['gambar' => $newDbPath]);
                            }
                        }

                        return $record;
                    }),
                DeleteAction::make()
                    ->button()
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Hapus')
                    ->modalDescription('Apakah yakin ingin menghapus data ini? File logo di server juga akan dihapus permanen.')
                    ->modalSubmitActionLabel('Ya, Hapus')
                    // Kita gunakan before() agar data record masih tersedia untuk diambil path gambarnya
                    ->before(function ($record) {
                        // 1. Ambil path gambar dari kolom 'gambar' (asumsi nama kolomnya 'gambar')
                        // Nilai ini biasanya berisi: media/company-internal/singkatan/logo/id/namafile.jpg
                        $imagePath = $record->gambar;

                        if ($imagePath) {
                            // 2. Hapus file spesifik menggunakan disk custom kamu
                            if (Storage::disk('public_folder')->exists($imagePath)) {
                                Storage::disk('public_folder')->delete($imagePath);
                            }

                            // 3. OPSIONAL: Hapus seluruh folder ID tersebut agar tidak meninggalkan folder kosong
                            // Ini akan menghapus folder: public/media/company-internal/{singkatan}/logo/{id}
                            $directory = "media/company-internal/{$record->singkatan}/logo/{$record->id}";
                            if (Storage::disk('public_folder')->exists($directory)) {
                                Storage::disk('public_folder')->deleteDirectory($directory);
                            }
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
