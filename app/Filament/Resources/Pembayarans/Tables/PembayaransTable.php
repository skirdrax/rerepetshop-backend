<?php

namespace App\Filament\Resources\Pembayarans\Tables;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\View;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PembayaransTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_pembayaran')
                    ->label('ID Pembayaran')
                    ->formatStateUsing(fn ($state) => 'PAY-' . str_pad($state, 4, '0', STR_PAD_LEFT))
                    ->sortable(),

                TextColumn::make('id_pesanan')
                    ->label('ID Pesanan')
                    ->formatStateUsing(fn ($state) => 'ORD-' . str_pad($state, 4, '0', STR_PAD_LEFT))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('status_bayar')
                    ->label('Status Bayar')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid'    => 'success',
                        'failed'  => 'danger',
                        default   => 'gray',
                    })
                    ->action(
                        Action::make('updateStatus')
                            ->form([
                                Select::make('status_bayar')
                                    ->label('Ganti Status Pembayaran')
                                    ->options([
                                        'pending' => 'ditunda',
                                        'paid'    => 'sudah bayar',
                                        'failed'  => 'gagal bayar',
                                    ])
                                    ->required(),
                            ])
                            ->action(function ($record, array $data): void {
                                $record->update($data);
                            })
                    ),

                TextColumn::make('metode_bayar')
                    ->label('Metode')
                    ->searchable(),

                TextColumn::make('jumlah_bayar')
                    ->label('Jumlah Bayar')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),

                TextColumn::make('waktu_bayar')
                    ->label('Tanggal Pembayaran')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}