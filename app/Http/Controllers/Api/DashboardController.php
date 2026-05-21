<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Keranjang;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $pelanggan = Pelanggan::where('email', $user->email)->first();

        if (!$pelanggan) {
            return response()->json([
                'message' => 'Data pelanggan tidak ditemukan.'
            ], 404);
        }

        $totalPesanan = Pesanan::where('id_pelanggan', $pelanggan->id_pelanggan)->count();
        $jumlahBaru = Pesanan::where('id_pelanggan', $pelanggan->id_pelanggan)
            ->where('status_pesanan', 'baru')
            ->count();

        $jumlahDiproses = Pesanan::where('id_pelanggan', $pelanggan->id_pelanggan)
            ->where('status_pesanan', 'diproses')
            ->count();

        $jumlahDikirim = Pesanan::where('id_pelanggan', $pelanggan->id_pelanggan)
            ->where('status_pesanan', 'dikirim')
            ->count();

        $jumlahSelesai = Pesanan::where('id_pelanggan', $pelanggan->id_pelanggan)
            ->where('status_pesanan', 'selesai')
            ->count();

        $keranjang = Keranjang::with('items')
            ->where('id_pelanggan', $pelanggan->id_pelanggan)
            ->first();

        $totalItemCart = $keranjang ? $keranjang->items->sum('qty') : 0;

        $recentOrder = Pesanan::where('id_pelanggan', $pelanggan->id_pelanggan)
            ->latest('id_pesanan')
            ->first();

        return response()->json([
            'message' => 'Dashboard customer berhasil diambil.',
            'data' => [
                'customer' => [
                    'id_pelanggan' => $pelanggan->id_pelanggan,
                    'nama' => $pelanggan->nama,
                    'email' => $pelanggan->email,
                ],
                'ringkasan_pesanan' => [
                    'total_pesanan' => $totalPesanan,
                    'baru' => $jumlahBaru,
                    'diproses' => $jumlahDiproses,
                    'dikirim' => $jumlahDikirim,
                    'selesai' => $jumlahSelesai,
                ],
                'cart' => [
                    'total_item' => $totalItemCart,
                    'total_harga' => $keranjang?->total ?? 0,
                ],
                'pesanan_terakhir' => $recentOrder ? [
                    'id_pesanan' => $recentOrder->id_pesanan,
                    'tanggal_pesanan' => $recentOrder->tanggal_pesanan,
                    'total' => $recentOrder->total,
                    'status_pesanan' => $recentOrder->status_pesanan,
                ] : null,
            ]
        ]);
    }
}