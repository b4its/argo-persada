<?php

namespace App\Filament\Resources\Superadmin\Akuns;

use App\Filament\Resources\Superadmin\Akuns\Pages\CreateAkun;
use App\Filament\Resources\Superadmin\Akuns\Pages\EditAkun;
use App\Filament\Resources\Superadmin\Akuns\Pages\ListAkuns;
use App\Filament\Resources\Superadmin\Akuns\Schemas\AkunForm;
use App\Filament\Resources\Superadmin\Akuns\Tables\AkunsTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AkunResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'users';

    public static function form(Schema $schema): Schema
    {
        return AkunForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AkunsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Akun';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-user-group'; // bisa diganti icon lain
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAkuns::route('/'),
            // 'create' => CreateAkun::route('/create'),
            // 'edit' => EditAkun::route('/{record}/edit'),
        ];
    }
}
