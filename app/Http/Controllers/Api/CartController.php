<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Keranjang;
use App\Models\KeranjangItem;
use App\Models\Pelanggan;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    protected function resolvePelanggan(Request $request): ?Pelanggan
    {
        $user = $request->user();

        if ($user?->pelanggan_id) {
            return Pelanggan::find($user->pelanggan_id);
        }

        return Pelanggan::where('email', $user?->email)->first();
    }

    protected function recalculateCartTotal(Keranjang $keranjang): void
    {
        $keranjang->total = KeranjangItem::where('id_keranjang', $keranjang->id_keranjang)->sum('subtotal');
        $keranjang->save();
    }

    public function add(Request $request)
    {
        $request->validate([
            'id_produk' => 'required',
            'qty' => 'required|integer|min:1',
        ]);

        $pelanggan = $this->resolvePelanggan($request);

        if (! $pelanggan) {
            return response()->json([
                'success' => false,
                'message' => 'Data pelanggan tidak ditemukan',
            ], 404);
        }

        $produk = Produk::findOrFail($request->id_produk);

        if ($produk->stok < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Stok produk habis',
            ], 422);
        }

        DB::transaction(function () use ($pelanggan, $produk, $request) {
            $produk = Produk::where('id_produk', $produk->id_produk)
                ->lockForUpdate()
                ->firstOrFail();

            $keranjang = Keranjang::firstOrCreate(
                ['id_pelanggan' => $pelanggan->id_pelanggan],
                ['total' => 0]
            );

            $item = KeranjangItem::where('id_keranjang', $keranjang->id_keranjang)
                ->where('id_produk', $produk->id_produk)
                ->lockForUpdate()
                ->first();

            $requestedQty = (int) $request->qty;
            $nextQty = ($item?->qty ?? 0) + $requestedQty;

            if ($nextQty > $produk->stok) {
                throw new HttpResponseException(response()->json([
                    'success' => false,
                    'message' => 'Jumlah melebihi stok tersedia',
                    'data' => [
                        'stok_tersedia' => $produk->stok,
                    ],
                ], 422));
            }

            if ($item) {
                $item->qty = $nextQty;
            } else {
                $item = new KeranjangItem();
                $item->id_keranjang = $keranjang->id_keranjang;
                $item->id_produk = $produk->id_produk;
                $item->qty = $requestedQty;
                $item->harga_satuan = $produk->harga;
            }

            $item->subtotal = $item->qty * $item->harga_satuan;
            $item->save();

            $this->recalculateCartTotal($keranjang);
        });

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan ke keranjang',
        ]);
    }

    public function cart(Request $request)
    {
        $pelanggan = $this->resolvePelanggan($request);

        if (! $pelanggan) {
            return response()->json([
                'success' => false,
                'message' => 'Data pelanggan tidak ditemukan',
            ], 404);
        }

        $keranjang = Keranjang::with('items.produk')
            ->where('id_pelanggan', $pelanggan->id_pelanggan)
            ->first();

        return response()->json([
            'success' => true,
            'data' => $keranjang,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'qty' => 'required|integer|min:1',
        ]);

        $pelanggan = $this->resolvePelanggan($request);

        if (! $pelanggan) {
            return response()->json([
                'success' => false,
                'message' => 'Data pelanggan tidak ditemukan',
            ], 404);
        }

        $item = KeranjangItem::with(['keranjang', 'produk'])
            ->where('id_item', $id)
            ->firstOrFail();

        if (! $item->keranjang || $item->keranjang->id_pelanggan !== $pelanggan->id_pelanggan) {
            return response()->json([
                'success' => false,
                'message' => 'Item keranjang tidak ditemukan',
            ], 404);
        }

        DB::transaction(function () use ($item, $request) {
            $lockedItem = KeranjangItem::with(['keranjang', 'produk'])
                ->where('id_item', $item->id_item)
                ->lockForUpdate()
                ->firstOrFail();

            $produk = Produk::where('id_produk', $lockedItem->id_produk)
                ->lockForUpdate()
                ->firstOrFail();

            $safeQty = (int) $request->qty;

            if ($safeQty > $produk->stok) {
                throw new HttpResponseException(response()->json([
                    'success' => false,
                    'message' => 'Jumlah melebihi stok tersedia',
                    'data' => [
                        'stok_tersedia' => $produk->stok,
                    ],
                ], 422));
            }

            $lockedItem->qty = $safeQty;
            $lockedItem->subtotal = $lockedItem->qty * $lockedItem->harga_satuan;
            $lockedItem->save();

            $this->recalculateCartTotal($lockedItem->keranjang);
        });

        return response()->json([
            'success' => true,
            'message' => 'Jumlah item berhasil diperbarui',
            'data' => $item->fresh(),
        ]);
    }

    public function remove(Request $request, $id)
    {
        $pelanggan = $this->resolvePelanggan($request);

        if (! $pelanggan) {
            return response()->json([
                'success' => false,
                'message' => 'Data pelanggan tidak ditemukan',
            ], 404);
        }

        $item = KeranjangItem::with('keranjang')
            ->where('id_item', $id)
            ->firstOrFail();

        if (! $item->keranjang || $item->keranjang->id_pelanggan !== $pelanggan->id_pelanggan) {
            return response()->json([
                'success' => false,
                'message' => 'Item keranjang tidak ditemukan',
            ], 404);
        }

        $keranjang = $item->keranjang;
        $item->delete();

        $this->recalculateCartTotal($keranjang);

        return response()->json([
            'success' => true,
            'message' => 'Item dihapus dari keranjang',
        ]);
    }
}
