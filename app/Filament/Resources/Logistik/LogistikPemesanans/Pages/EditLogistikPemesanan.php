<?php

namespace App\Filament\Resources\Logistik\LogistikPemesanans\Pages;

use App\Filament\Resources\Logistik\LogistikPemesanans\LogistikPemesananResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLogistikPemesanan extends EditRecord
{
    protected static string $resource = LogistikPemesananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
