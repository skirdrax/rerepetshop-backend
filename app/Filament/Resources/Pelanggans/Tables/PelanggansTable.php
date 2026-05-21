<?php

namespace App\Filament\Resources\Pelanggans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PelanggansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
    TextColumn::make('id_pelanggan')
        ->label('ID Pelanggan')
        ->formatStateUsing(fn ($state) => 'PLG-' . str_pad((string) $state, 4, '0', STR_PAD_LEFT))
        ->sortable(),

    TextColumn::make('nama')
        ->searchable(),

    TextColumn::make('email')
        ->label('Email address')
        ->searchable(),

    TextColumn::make('no_hp')
        ->searchable(),

    TextColumn::make('alamat')
                ->label('Alamat')
                ->searchable(),    

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
 