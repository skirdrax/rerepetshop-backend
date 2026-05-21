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
    Schema::create('produk', function (Blueprint $table) {
        $table->increments('id_produk');
        $table->unsignedInteger('id_kategori');
        $table->string('nama_produk', 120);
        $table->decimal('harga', 12, 2)->default(0);
        $table->integer('stok')->default(0);
        $table->text('deskripsi')->nullable();
        $table->string('foto', 255)->nullable();
        $table->timestamps();

        $table->foreign('id_kategori')
              ->references('id_kategori')
              ->on('kategori')
              ->onUpdate('cascade');
    });
}

public function down(): void
{
    Schema::dropIfExists('produk');
}
};
