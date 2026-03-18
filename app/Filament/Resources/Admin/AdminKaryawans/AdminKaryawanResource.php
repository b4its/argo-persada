<?php

namespace App\Filament\Resources\Admin\AdminKaryawans;

use App\Filament\Resources\Admin\AdminKaryawans\Pages\CreateAdminKaryawan;
use App\Filament\Resources\Admin\AdminKaryawans\Pages\EditAdminKaryawan;
use App\Filament\Resources\Admin\AdminKaryawans\Pages\ListAdminKaryawans;
use App\Filament\Resources\Admin\AdminKaryawans\Schemas\AdminKaryawanForm;
use App\Filament\Resources\Admin\AdminKaryawans\Tables\AdminKaryawansTable;
use App\Models\AdminKaryawan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AdminKaryawanResource extends Resource
{
    protected static ?string $model = AdminKaryawan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'users';

    public static function form(Schema $schema): Schema
    {
        return AdminKaryawanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdminKaryawansTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdminKaryawans::route('/'),
            'create' => CreateAdminKaryawan::route('/create'),
            'edit' => EditAdminKaryawan::route('/{record}/edit'),
        ];
    }
}
