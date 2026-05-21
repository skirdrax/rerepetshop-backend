<?php

namespace App\Filament\Resources\Pembayarans\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;

class PembayaranForm
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
                $item->id_pesanan => 'ORD-' . str_pad((string) $item->id_pesanan, 4, '0', STR_PAD_LEFT)
                    . ' - ' . $item->tanggal_pesanan,
            ])
            ->toArray()
    )
    ->searchable()
    ->required(),
                
            TextInput::make('ref_gateway')  
                    ->label('Ref Gateway'),

                TextInput::make('metode_bayar')
                    ->label('Metode Pembayaran')
                    ->required(),

                TextInput::make('jumlah_bayar')
                    ->label('Jumlah Bayar')
                    ->numeric()
                    ->required()
                    ->default(0),

                Select::make('status_bayar')
                    ->label('Status Pembayaran')
                    ->options([
                        'pending' => 'ditunda',
                        'paid' => 'sudah dibayar',
                        'failed' => 'gagal dibayar',
                        'expired' => 'kadaluarsa',

                TextColumn::make('waktu_bayar') // Sesuaikan dengan nama kolom di DB kamu
                ->label('Tanggal Pembayaran')
                ->dateTime('M d, Y H:i:s') // Format agar rapi seperti di gambar
                ->sortable(),        
                    ])
                    ->required(),
                    
            ]);
    }
}