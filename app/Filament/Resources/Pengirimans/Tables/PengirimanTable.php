<?php

namespace App\Filament\Resources\Pengirimans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;

class PengirimanTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                 TextColumn::make('id_pesanan')
        ->label('ID Pesanan')
        ->formatStateUsing(fn ($state) => 'ORD-' . str_pad($state, 4, '0', STR_PAD_LEFT))
        ->sortable(),

        TextColumn::make('resi')
                    ->searchable(),        
        
        TextColumn::make('status_kirim')
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
    })
    ->action(
        Action::make('ubah_status_kirim')
            ->form([
                Select::make('status_kirim')
                    ->label('Ubah Status Kirim')
                    ->options([
                        'diproses' => 'Diproses',
                        'dikirim'  => 'Dikirim',
                        'diterima' => 'Diterima',
                    ])
                    ->required(),
            ])
            ->action(function ($record, array $data): void {
                $record->update(['status_kirim' => $data['status_kirim']]);

                // Kalau diterima → pesanan jadi selesai
                if ($data['status_kirim'] === 'diterima') {
                    $record->pesanan()->update(['status_pesanan' => 'selesai']);
                }
            })
    ),            

        TextColumn::make('tanggal_kirim')
                    ->dateTime()
                    ->sortable(),
        
        TextColumn::make('pesanan.alamat_kirim')
        ->label('Alamat Kirim')
        ->searchable()
        ->wrap(),
        
        TextColumn::make('kurir')
                    ->label('Kurir')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                    'jne' => 'danger',
                    'ojol'  => 'success',
                    'internal' => 'success',
                    default    => 'gray',
                })
                    ->formatStateUsing(fn ($state) => match($state) {
                        'jne'      => 'JNE Ekspress',
                        'ojol'     => 'Gosend/Grab',
                        'internal' => 'Kurir Internal (Udin)',
                        'menunggu_kurir'  => 'Pilih kurir',
                        default    => $state ?? '-',
                    })
                    ->action(
                        Action::make('ubah_kurir')
                            ->form([
                                Select::make('kurir')
                                    ->label('Pilih Kurir')
                                    ->options([
                                        'jne'      => 'JNE Ekspress',
                                        'ojol'     => 'Gosend/Grab',
                                        'internal' => 'Kurir Internal (Udin)',
                                        'menunggu_kurir'  => '⏳ Menunggu Kurir',
                                    ])
                                    ->required(),
                            ])
                            ->action(function ($record, array $data): void {
                                $record->update(['kurir' => $data['kurir']]);
                            })
                    ),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
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