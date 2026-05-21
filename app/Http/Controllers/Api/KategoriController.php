<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        $kategori = Kategori::orderBy('id_kategori', 'desc')->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar kategori berhasil diambil',
            'data' => $kategori,
        ]);
    }

    public function show($id)
    {
        $kategori = Kategori::find($id);

        if (! $kategori) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail kategori berhasil diambil',
            'data' => $kategori,
        ]);
    }

    public function produkByKategori($id)
    {
        $kategori = Kategori::with('produks')->find($id);

        if (! $kategori) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Produk berdasarkan kategori berhasil diambil',
            'data' => $kategori,
        ]);
    }
}