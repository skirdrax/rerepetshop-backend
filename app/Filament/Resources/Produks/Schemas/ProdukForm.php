<?php

namespace App\Filament\Resources\Produks\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;

class ProdukForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_kategori')
                    ->label('Kategori')
                    ->options(\App\Models\Kategori::pluck('nama_kategori', 'id_kategori'))
                    ->searchable()
                    ->required(),
                TextInput::make('nama_produk')
                    ->required(),
                TextInput::make('harga')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('stok')
                    ->required()
                    ->numeric()
                    ->default(0),
                Textarea::make('deskripsi')
                    ->default(null)
                    ->columnSpanFull(),
                FileUpload::make('foto')
                    ->image()
                    ->disk('public')
                    ->directory('produk')
                    ->imagePreviewHeight('200')
                    ->visibility('public'),
            ]);
    }
}
