<?php

namespace App\Filament\Resources\Admin\AdminCompanyInternals\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminCompanyInternalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                TextInput::make("name")
                    ->label("Nama")
                    ->required(),

                TextInput::make("singkatan")
                    ->label("Singkatan")
                    ->required(),
                
                TextInput::make("phone_number")
                    ->label("Nomor Telepon")
                    ->maxLength(12),

                Select::make('is_ppn')
                    ->label('PPN')
                    ->options([
                        0 => 'Tidak',
                        1 => 'Iya',
                    ])
                    ->default(0)
                    ->native(false) 
                    ->required(),

                Textarea::make("alamat")
                    ->label("Alamat")
                    ->rows(3)
                    ->columnSpanFull(),

                TextInput::make("no_rekening")
                    ->label("No Rekening")
                    ->maxLength(255),

                TextInput::make("nama_bank")
                    ->label("Nama Bank")
                    ->maxLength(255),

                TextInput::make("nama_pemilik_bank")
                    ->label("Nama Pemilik Rekening")
                    ->maxLength(255),

                FileUpload::make('gambar')
                    ->disk('public_folder')
                    ->directory(function ($record, $get) {
                        // Ambil singkatan dan slug agar "PT SM" jadi "pt_sm"
                        $singkatan = Str::slug($record?->singkatan ?? $get('singkatan') ?? 'default', '_');
                        
                        if ($record?->id) {
                            return "media/company-internal/{$singkatan}/logo/{$record->id}";
                        }
                        
                        return "media/company-internal/{$singkatan}/logo/temp";
                    })
                    ->getUploadedFileNameForStorageUsing(function ($file, $record) {
                        $ext = $file->getClientOriginalExtension();
                        $datetime = now()->format('Ymd_His');
                        $id = $record?->id ?? 'new';
                        return "logo_{$datetime}_{$id}.{$ext}";
                    })
                    // === TAMBAHKAN INI UNTUK MENGHENTIKAN LOOP ===
                    // 1. Matikan pratinjau sementara untuk memastikan masalah di level URL/Path
                    ->previewable(false) 
                    
                    // 2. Cegah error jika file fisik hilang (Trik untuk menimpa method yang hilang sebelumnya)
                    ->extraAttributes(['data-on-error' => 'this.style.display="none"']) 
                    // ============================================

                    ->visibility('public')
                    ->preserveFilenames(false)
                    ->deleteUploadedFileUsing(fn ($file) => Storage::disk('public_folder')->delete($file))


                    
            ]);
    }
}
