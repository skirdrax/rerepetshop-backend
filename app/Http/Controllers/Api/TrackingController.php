<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function track($id_pesanan) 
    {
        // Ambil data pesanan beserta relasi pengirimannya
        $pesanan = Pesanan::with('pengiriman')->find($id_pesanan);

        if (!$pesanan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pesanan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'order_id' => $pesanan->kode_pesanan, // Pakai accessor ORD-xxxx
                'customer_status' => $pesanan->status_pesanan,
                'shipping' => [
                    'status' => $pesanan->pengiriman->status_kirim ?? 'Sedang dikemas',
                    'kurir' => $pesanan->pengiriman->kurir ?? '-',
                    'resi' => $pesanan->pengiriman->resi ?? '-',
                    'tanggal_kirim' => $pesanan->pengiriman->tanggal_kirim ?? '-',
                ]
            ]
        ]);
    }
}