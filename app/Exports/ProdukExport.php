<?php

namespace App\Exports;

use App\Models\Produk;
use Spatie\SimpleExcel\SimpleExcelWriter;

class ProdukExport
{
    public static function export(string $path): void
    {
        $writer = SimpleExcelWriter::create($path);

        $writer->addHeader([
            'id_produk',
            'id_kategori',
            'nama_produk',
            'harga',
            'stok',
        ]);

        Produk::all()->each(function ($produk) use ($writer) {
            $writer->addRow([
                'id_produk'   => $produk->id_produk,
                'id_kategori' => $produk->id_kategori,
                'nama_produk' => $produk->nama_produk,
                'harga'       => $produk->harga,
                'stok'        => $produk->stok,
            ]);
        });

        $writer->close();
    }
}