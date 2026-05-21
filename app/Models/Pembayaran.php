<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';
    protected $primaryKey = 'id_pembayaran';

    protected $fillable = [
        'id_pesanan',
        'metode_bayar',
        'jumlah_bayar',
        'status_bayar',
        'ref_gateway',
        'snap_token',
        'waktu_bayar',
    ];

    protected static function booted(): void
    {
        static::updated(function ($pembayaran) {
            if ($pembayaran->wasChanged('status_bayar') && $pembayaran->status_bayar === 'paid') {
                $pesanan = $pembayaran->pesanan;

                if ($pesanan && in_array($pesanan->status_pesanan, ['baru', 'menunggu_verifikasi'])) {
                    $pesanan->status_pesanan = 'diproses';
                    $pesanan->save();
                }
            }
        });
    }

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }
}
