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
    Schema::create('pesanan_detail', function (Blueprint $table) {
        $table->increments('id_detail');
        $table->unsignedInteger('id_pesanan');
        $table->unsignedInteger('id_produk');
        $table->integer('qty')->default(1);
        $table->decimal('harga_satuan', 12, 2)->default(0);
        $table->decimal('subtotal', 12, 2)->default(0);

        $table->foreign('id_pesanan')
              ->references('id_pesanan')
              ->on('pesanan')
              ->onDelete('cascade')
              ->onUpdate('cascade');

        $table->foreign('id_produk')
              ->references('id_produk')
              ->on('produk')
              ->onUpdate('cascade');
    });
}

public function down(): void
{
    Schema::dropIfExists('pesanan_detail');
}
};
