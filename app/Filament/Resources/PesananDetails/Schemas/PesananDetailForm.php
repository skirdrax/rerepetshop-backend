<?php

namespace App\Filament\Resources\PesananDetails\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PesananDetailForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_pesanan')
                    ->label('Pesanan')
                    ->options(
                        \App\Models\Pesanan::query()
                            ->orderBy('id_pesanan', 'desc')
                            ->get()
                            ->mapWithKeys(fn ($item) => [
                                $item->id_pesanan => 'ORD-' . str_pad($item->id_pesanan, 4, '0', STR_PAD_LEFT),
                            ])
                            ->toArray()
                    )
                    ->searchable()
                    ->required(),

                Select::make('id_produk')
                    ->label('Produk')
                    ->options(\App\Models\Produk::pluck('nama_produk', 'id_produk')->toArray())
                    ->searchable()
                    ->required(),

                TextInput::make('qty')
                    ->label('Jumlah')
                    ->numeric()
                    ->required()
                    ->default(1),

                TextInput::make('harga_satuan')
                    ->label('Harga Satuan')
                    ->numeric()
                    ->required()
                    ->default(0),

                TextInput::make('subtotal')
                    ->label('Subtotal')
                    ->numeric()
                    ->required()
                    ->default(0),
            ]);
    }
}