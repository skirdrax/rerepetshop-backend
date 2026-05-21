<?php

namespace App\Filament\Resources\Produks\Tables;

use App\Exports\ProdukExport;
use App\Imports\ProdukImport;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn; // ← tambah ini di atas

class ProduksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->headerActions([
                Action::make('exportExcel')
                    ->label('Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function () {
                        $fileName = 'produk-export-' . now()->format('Y-m-d_H-i-s') . '.xlsx';
                        $path = storage_path('app/public/' . $fileName);

                        ProdukExport::export($path);

                        return response()->download($path)->deleteFileAfterSend(true);
                    }),

                Action::make('importCsv')
                    ->label('Import CSV')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->form([
                        FileUpload::make('file')
                            ->label('Upload File CSV')
                            ->required()
                            ->disk('public')
                            ->directory('imports')
                            ->acceptedFileTypes([
                                'text/csv',
                                'application/vnd.ms-excel',
                                'application/csv',
                            ]),
                    ])
                    ->action(function (array $data) {
                        $path = storage_path('app/public/' . $data['file']);

                        ProdukImport::import($path);

                        Notification::make()
                            ->title('Import berhasil')
                            ->success()
                            ->send();
                    }),
            ])
            ->columns([
                TextColumn::make('id_produk')
                    ->label('ID PRODUK')
                    ->formatStateUsing(fn ($state) => 'PRO-' . str_pad($state, 4, '0', STR_PAD_LEFT))
                    ->sortable(),

                TextColumn::make('id_kategori')
                    ->label('Kategori')
                    ->formatStateUsing(function ($state, $record) {
                        return 'CAT-' . str_pad($record->kategori->id_kategori, 4, '0', STR_PAD_LEFT)
                            . ' - ' . $record->kategori->nama_kategori;
                    })
                    ->sortable(),

                TextColumn::make('nama_produk')
                    ->label('Nama Produk')
                    ->searchable(),

                TextColumn::make('harga')
                    ->label('Harga')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('stok')
                    ->label('Stok')
                    ->numeric()
                    ->sortable(),
                
                ImageColumn::make('foto')  // ← tambahkan ini
                    ->label('Foto')
                    ->disk('public')
                    ->height(50)
                    ->width(50),

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
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
} 