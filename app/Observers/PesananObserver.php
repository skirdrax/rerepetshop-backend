<?php

namespace App\Observers;

use App\Models\Pesanan;
use App\Models\Pengiriman;

class PesananObserver
{
    public function updating(Pesanan $pesanan): void
{
    if ($pesanan->isDirty('status_pesanan') && $pesanan->status_pesanan === 'dikirim') {
        
        // Cek apakah pembayaran sudah paid
        $pembayaran = $pesanan->pembayaran;
        
        if (!$pembayaran || $pembayaran->status_bayar !== 'paid') {
            // Batalkan perubahan status
            $pesanan->status_pesanan = $pesanan->getOriginal('status_pesanan');
            
            // Tampilkan notifikasi error
            \Filament\Notifications\Notification::make()
                ->title('Gagal Mengubah Status')
                ->body('Pesanan belum dibayar! Status tidak dapat diubah ke "Dikirim".')
                ->danger()
                ->send();
            
            return;
        }

        Pengiriman::firstOrCreate(
            ['id_pesanan' => $pesanan->id_pesanan],
            [
                'status_kirim'  => 'diproses',
                'kurir'         => 'menunggu_kurir',
                'resi'          => 'RESI-' . now()->format('Ymd') . '-' . str_pad($pesanan->id_pesanan, 4, '0', STR_PAD_LEFT),
                'tanggal_kirim' => now(),
            ]
        );
    }
}
}