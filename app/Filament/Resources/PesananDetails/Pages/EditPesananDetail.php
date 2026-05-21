<?php

namespace App\Filament\Resources\PesananDetails\Pages;

use App\Filament\Resources\PesananDetails\PesananDetailResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPesananDetail extends EditRecord
{
    protected static string $resource = PesananDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
