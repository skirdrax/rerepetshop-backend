<?php

namespace App\Filament\Resources\Pesanans\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PesananForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_pelanggan')
                    ->label('Pelanggan')
                    ->options(
                        \App\Models\Pelanggan::query()
                            ->orderBy('id_pelanggan', 'desc')
                            ->get()
                            ->mapWithKeys(fn ($item) => [
                                $item->id_pelanggan => 'PLG-' . str_pad($item->id_pelanggan, 4, '0', STR_PAD_LEFT)  . $item->nama_pelanggan,
                            ])
                            ->toArray()
                    )
                    ->searchable()
                    ->required(),

                DateTimePicker::make('tanggal_pesanan')
                    ->required(),

                Textarea::make('alamat_kirim')
                    ->required()
                    ->columnSpanFull(),

                Textarea::make('no_telp')
                    ->required()
                    ->columnSpanFull(),    

                TextInput::make('total')
                    ->numeric()
                    ->required()
                    ->default(0),

                Select::make('status_pesanan')
                    ->options([
                        'baru' => 'Baru',
                        'diproses' => 'Diproses',
                        'dikirim' => 'Dikirim',
                        'selesai' => 'Selesai',
                        'batal' => 'Dibatalkan',
                    ])
                    ->required(),
            ]);
    }
}
