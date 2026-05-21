<?php

namespace App\Filament\Resources\PesananDetails\Pages;

use App\Filament\Resources\PesananDetails\PesananDetailResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPesananDetails extends ListRecords
{
    protected static string $resource = PesananDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
