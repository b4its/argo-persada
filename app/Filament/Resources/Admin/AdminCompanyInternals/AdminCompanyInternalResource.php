<?php

namespace App\Filament\Resources\Admin\AdminCompanyInternals;

use App\Filament\Resources\Admin\AdminCompanyInternals\Pages\CreateAdminCompanyInternal;
use App\Filament\Resources\Admin\AdminCompanyInternals\Pages\EditAdminCompanyInternal;
use App\Filament\Resources\Admin\AdminCompanyInternals\Pages\ListAdminCompanyInternals;
use App\Filament\Resources\Admin\AdminCompanyInternals\Schemas\AdminCompanyInternalForm;
use App\Filament\Resources\Admin\AdminCompanyInternals\Tables\AdminCompanyInternalsTable;
use App\Models\CompanyInternal;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AdminCompanyInternalResource extends Resource
{
    protected static ?string $model = CompanyInternal::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'company_internal';
    protected static ?string $slug = 'company-internal';

    public static function form(Schema $schema): Schema
    {
        return AdminCompanyInternalForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdminCompanyInternalsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Perusahaan Internal';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-user-group'; // bisa diganti icon lain
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdminCompanyInternals::route('/'),
            // 'create' => CreateAdminCompanyInternal::route('/create'),
            // 'edit' => EditAdminCompanyInternal::route('/{record}/edit'),
        ];
    }
}
