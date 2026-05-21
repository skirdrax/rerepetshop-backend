<?php

namespace App\Imports;

use App\Models\Produk;
use Spatie\SimpleExcel\SimpleExcelReader;

class ProdukImport
{
    public static function import(string $path): void
    {
        SimpleExcelReader::create($path)
            ->useDelimiter(';') // 🔥 INI KUNCINYA
            ->getRows()
            ->each(function (array $row) {

                $cleanRow = [];
                foreach ($row as $key => $value) {
                    $cleanKey = trim(str_replace("\xEF\xBB\xBF", '', $key));
                    $cleanRow[$cleanKey] = trim((string) $value);
                }

                $idProduk = (int) ($cleanRow['id_produk'] ?? 0);
                $idKategori = (int) ($cleanRow['id_kategori'] ?? 0);

                if (!$idProduk || !$idKategori) {
                    return;
                }

                Produk::updateOrCreate(
                    ['id_produk' => $idProduk],
                    [
                        'id_kategori' => $idKategori,
                        'nama_produk' => $cleanRow['nama_produk'] ?? '',
                        'harga'       => $cleanRow['harga'] ?? 0,
                        'stok'        => $cleanRow['stok'] ?? 0,
                    ]
                );
            });
    }
}