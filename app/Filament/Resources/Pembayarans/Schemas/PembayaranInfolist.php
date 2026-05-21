<?php

namespace App\Filament\Resources\Pembayarans\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PembayaranInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id_pesanan')
                    ->numeric(),
                TextEntry::make('metode_bayar'),
                TextEntry::make('status_bayar'),
                TextEntry::make('ref_gateway'),
                TextEntry::make('waktu_bayar')
                    ->dateTime(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
