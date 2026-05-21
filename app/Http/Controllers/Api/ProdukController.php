<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_kategori' => 'nullable|integer',
            'search' => 'nullable|string|max:80',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter pencarian tidak valid',
                'errors' => $validator->errors(),
            ], 422);
        }

        $query = Produk::with('kategori');

        if ($request->filled('id_kategori')) {
            $query->where('id_kategori', $request->id_kategori);
        }

        $search = trim((string) $request->input('search', ''));

        if ($search !== '') {
            $query->where('nama_produk', 'like', '%' . $search . '%');
        }

        $produk = $query
            ->orderBy('id_produk', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar produk berhasil diambil',
            'data' => $produk,
        ]);
    }

    public function show($id)
    {
        $produk = Produk::with('kategori')->find($id);

        if (! $produk) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail produk berhasil diambil',
            'data' => $produk,
        ]);
    }
}
