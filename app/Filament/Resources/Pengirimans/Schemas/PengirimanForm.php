<?php

namespace App\Filament\Resources\Pengirimans\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PengirimanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_pesanan')
                        ->label('Pilih Pesanan')
                        ->options(
                        \App\Models\Pesanan::all()->pluck('id_pesanan', 'id_pesanan')
                        ->mapWithKeys(fn ($id) => [$id => 'ORD-' . str_pad($id, 4, '0', STR_PAD_LEFT)])
                        )
                        ->searchable()
                        ->preload() // Tambahkan ini biar datanya dimuat duluan
                        ->required(),

                Select::make('status_kirim')
                    ->options([
                        'diproses' => 'Diproses',
                        'dikirim' => 'Dikirim',
                        'diterima' => 'Diterima',
                    ])
                    ->default('diproses')
                    ->required(),

                Select::make('kurir')
                    ->options([
                        'menunggu_kurir' => '⏳ Menunggu Kurir',
                        'jne' => 'JNE Ekspress',
                        'ojol' => 'Gosend/grab',
                        'internal' => 'Kurir Internal',
                    ])
                    ->required(),

                TextInput::make('resi'),

                DateTimePicker::make('tanggal_kirim'),
            ]);
    }
}