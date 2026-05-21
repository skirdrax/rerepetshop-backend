<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    // Hapus dulu kalau tabelnya nyangkut
    Schema::dropIfExists('favorites');

    Schema::create('favorites', function (Blueprint $table) {
        $table->id();
        
        // Pakai unsignedInteger (bukan BigInteger) biar pas sama INT(11) di phpMyAdmin
        $table->unsignedInteger('pelanggan_id');
        $table->unsignedInteger('produk_id');

        $table->foreign('pelanggan_id')
              ->references('id_pelanggan')
              ->on('pelanggan')
              ->onDelete('cascade');

        $table->foreign('produk_id')
              ->references('id_produk')
              ->on('produk')
              ->onDelete('cascade');

        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
