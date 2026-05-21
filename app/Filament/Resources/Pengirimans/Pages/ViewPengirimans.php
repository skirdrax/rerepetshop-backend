<?php

namespace App\Filament\Resources\Pengirimans\Pages;

use App\Filament\Resources\Pengirimans\PengirimanResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPengiriman extends ViewRecord
{
    protected static string $resource = PengirimanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}