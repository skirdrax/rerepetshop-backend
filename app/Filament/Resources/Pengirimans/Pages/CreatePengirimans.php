<?php

namespace App\Filament\Resources\Pengirimans\Pages;

use App\Filament\Resources\Pengirimans\PengirimanResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePengiriman extends CreateRecord
{
    protected static string $resource = PengirimanResource::class;

    protected function afterCreate(): void
    {
        $pengiriman = $this->record;
        
        // Otomatis update status di tabel pesanan
        if ($pengiriman->status_kirim === 'dikirim') {
            $pengiriman->pesanan()->update([
                'status_pesanan' => 'dikirim' // Pastikan value status ini ada di enum/database pesanan
            ]);
        }
    }
}