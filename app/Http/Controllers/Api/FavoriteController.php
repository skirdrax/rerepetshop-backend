<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Favorite;
use App\Models\Pelanggan;

class FavoriteController extends Controller
{
    protected function resolvePelangganId($user): ?int
    {
        if (! $user) {
            return null;
        }

        if (! empty($user->pelanggan_id)) {
            return (int) $user->pelanggan_id;
        }

        $pelanggan = Pelanggan::where('email', $user->email)->first();

        if (! $pelanggan) {
            return null;
        }

        if ($user->pelanggan_id !== $pelanggan->id_pelanggan) {
            $user->pelanggan_id = $pelanggan->id_pelanggan;
            $user->save();
        }

        return (int) $pelanggan->id_pelanggan;
    }

    public function index()
    {
        $user = auth('sanctum')->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $pelangganId = $this->resolvePelangganId($user);

        if (! $pelangganId) {
            return response()->json([
                'success' => false,
                'message' => 'Data pelanggan tidak ditemukan',
            ], 404);
        }

        $favorites = Favorite::with('produk')
            ->where('id_pelanggan', $pelangganId)
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $favorites,
        ]);
    }

    public function store(Request $request)
    {
        $user = auth('sanctum')->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $pelangganId = $this->resolvePelangganId($user);

        if (! $pelangganId) {
            return response()->json([
                'success' => false,
                'message' => 'Data pelanggan tidak ditemukan',
            ], 404);
        }

        $request->validate([
            'produk_id' => 'required|exists:produk,id_produk',
        ]);

        $data = [
            'id_pelanggan' => $pelangganId,
            'id_produk'    => $request->input('produk_id'),
        ];

        $exists = Favorite::where($data)->first();

        if ($exists) {
            $exists->delete();
            return response()->json([
                'success' => true,
                'message' => 'Dihapus dari favorit',
                'is_favorite' => false,
            ]);
        }

        $favorite = Favorite::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Ditambahkan ke favorit',
            'data' => $favorite,
            'is_favorite' => true,
        ]);
    }

    public function destroy($productId)
    {
        $user = auth('sanctum')->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $pelangganId = $this->resolvePelangganId($user);

        if (! $pelangganId) {
            return response()->json([
                'success' => false,
                'message' => 'Data pelanggan tidak ditemukan',
            ], 404);
        }

        Favorite::where('id_pelanggan', $pelangganId)
            ->where('id_produk', $productId)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dihapus dari favorit',
            'is_favorite' => false,
        ]);
    }
}
