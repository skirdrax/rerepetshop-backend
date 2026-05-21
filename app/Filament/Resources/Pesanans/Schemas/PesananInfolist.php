<?php

namespace App\Filament\Resources\Pesanans\Schemas;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PesananInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Pesanan')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('id_pelanggan')
                            ->label('ID Pelanggan')
                            ->numeric(),
                        TextEntry::make('tanggal_pesanan')
                            ->label('Tanggal Pesanan')
                            ->dateTime(),
                        TextEntry::make('total')
                            ->label('Total')
                            ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                        TextEntry::make('status_pesanan')
                            ->label('Status Pesanan')
                            ->badge()
                            ->color(fn ($state) => match($state) {
                                'baru'     => 'info',
                                'diproses' => 'warning',
                                'dikirim'  => 'primary',
                                'selesai'  => 'success',
                                'batal'    => 'danger',
                                default    => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'baru'     => 'Baru',
                                'diproses' => 'Diproses',
                                'dikirim'  => 'Dikirim',
                                'selesai'  => 'Selesai',
                                'batal'    => 'Dibatalkan',
                                default    => $state,
                            }),
                        TextEntry::make('created_at')
                            ->label('Dibuat')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Diperbarui')
                            ->dateTime(),
                    ]),

                Section::make('Informasi Pengiriman')
                    ->columns(2)
                    ->hidden(fn ($record) => $record->pengiriman === null)
                    ->schema([
                        TextEntry::make('pengiriman.status_kirim')
                            ->label('Status Kirim')
                            ->badge()
                            ->color(fn ($state) => match($state) {
                                'diproses' => 'warning',
                                'dikirim'  => 'primary',
                                'diterima' => 'success',
                                default    => 'gray',
                            })
                            ->formatStateUsing(fn ($state) => match($state) {
                                'diproses' => 'Diproses',
                                'dikirim'  => 'Dikirim',
                                'diterima' => 'Diterima',
                                default    => $state,
                            }),
                        TextEntry::make('pengiriman.kurir')
                            ->label('Kurir')
                            ->placeholder('Belum diisi'),
                        TextEntry::make('pengiriman.resi')
                            ->label('No. Resi')
                            ->placeholder('Belum diisi'),
                        TextEntry::make('pengiriman.tanggal_kirim')
                            ->label('Tanggal Kirim')
                            ->dateTime()
                            ->placeholder('Belum diisi'),
                    ]),
            ]);
    }
}