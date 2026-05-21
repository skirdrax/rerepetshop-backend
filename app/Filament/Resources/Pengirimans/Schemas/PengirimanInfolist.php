<?php

namespace App\Filament\Resources\Pengirimans\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PengirimanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id_pesanan')
                    ->numeric(),

                TextEntry::make('status_kirim'),
                TextEntry::make('kurir'),
                TextEntry::make('resi'),

                TextEntry::make('tanggal_kirim')
                    ->dateTime(),

                TextEntry::make('created_at')
                    ->dateTime(),

                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}