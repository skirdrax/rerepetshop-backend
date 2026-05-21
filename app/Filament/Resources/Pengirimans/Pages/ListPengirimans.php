<?php

namespace App\Filament\Resources\Pengirimans\Pages;

use App\Filament\Resources\Pengirimans\PengirimanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPengiriman extends ListRecords
{
    protected static string $resource = PengirimanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}