<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Keranjang;
use App\Models\KeranjangItem;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function checkout(Request $request)
    {
        $user = $request->user();

        $pelanggan = $user->pelanggan_id
            ? Pelanggan::find($user->pelanggan_id)
            : Pelanggan::where('email', $user->email)->first();

        if (! $pelanggan) {
            return response()->json([
                'success' => false,
                'message' => 'Data pelanggan tidak ditemukan',
            ], 404);
        }

        $validated = $request->validate([
            'alamat_kirim' => 'nullable|string',
            'no_telp' => 'nullable|string|max:20',
        ]);

        $alamatKirim = $validated['alamat_kirim'] ?? $pelanggan->alamat;
        $noTelp = $validated['no_telp'] ?? $pelanggan->no_hp;

        if (! $alamatKirim || ! $noTelp) {
            return response()->json([
                'success' => false,
                'message' => 'Lengkapi alamat dan nomor telepon Anda terlebih dahulu sebelum checkout.',
            ], 422);
        }

        $keranjang = Keranjang::where('id_pelanggan', $pelanggan->id_pelanggan)->first();

        if (! $keranjang) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang tidak ditemukan',
            ], 404);
        }

        try {
            $pesanan = DB::transaction(function () use ($pelanggan, $alamatKirim, $noTelp, $keranjang) {
                $lockedItems = KeranjangItem::with('produk')
                    ->where('id_keranjang', $keranjang->id_keranjang)
                    ->lockForUpdate()
                    ->get();

                if ($lockedItems->isEmpty()) {
                    throw new HttpResponseException(response()->json([
                        'success' => false,
                        'message' => 'Keranjang kosong',
                    ], 400));
                }

                foreach ($lockedItems as $item) {
                    $produk = Produk::where('id_produk', $item->id_produk)
                        ->lockForUpdate()
                        ->first();

                    if (! $produk) {
                        throw new HttpResponseException(response()->json([
                            'success' => false,
                            'message' => 'Produk pada keranjang tidak ditemukan',
                        ], 404));
                    }

                    if ($item->qty > $produk->stok) {
                        throw new HttpResponseException(response()->json([
                            'success' => false,
                            'message' => "Stok produk {$produk->nama_produk} tidak mencukupi",
                            'data' => [
                                'id_produk' => $produk->id_produk,
                                'stok_tersedia' => $produk->stok,
                                'qty_diminta' => $item->qty,
                            ],
                        ], 422));
                    }
                }

                $pesanan = Pesanan::create([
                    'id_pelanggan' => $pelanggan->id_pelanggan,
                    'tanggal_pesanan' => now(),
                    'alamat_kirim' => $alamatKirim,
                    'no_telp' => $noTelp,
                    'total' => $lockedItems->sum('subtotal'),
                    'status_pesanan' => 'baru',
                ]);

                foreach ($lockedItems as $item) {
                    $produk = Produk::where('id_produk', $item->id_produk)
                        ->lockForUpdate()
                        ->firstOrFail();

                    $produk->stok -= $item->qty;
                    $produk->save();

                    PesananDetail::create([
                        'id_pesanan' => $pesanan->id_pesanan,
                        'id_produk' => $item->id_produk,
                        'qty' => $item->qty,
                        'harga_satuan' => $item->harga_satuan,
                        'subtotal' => $item->subtotal,
                    ]);
                }

                KeranjangItem::where('id_keranjang', $keranjang->id_keranjang)->delete();
                $keranjang->total = 0;
                $keranjang->save();

                return $pesanan;
            });

            $pesanan->load('details.produk');

            return response()->json([
                'success' => true,
                'message' => 'Checkout berhasil',
                'data' => $pesanan,
            ], 201);
        } catch (\Throwable $th) {
            if ($th instanceof HttpResponseException) {
                throw $th;
            }

            return response()->json([
                'success' => false,
                'message' => 'Checkout gagal',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
