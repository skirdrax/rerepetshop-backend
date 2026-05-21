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
    // Ubah dari 'produks' menjadi 'produk'
    Schema::table('produk', function (Blueprint $table) {
        $table->integer('views')->default(0);
    });
}

public function down(): void
{
    // Samakan juga di sini menjadi 'produk'
    Schema::table('produk', function (Blueprint $table) {
        $table->dropColumn('views');
    });
}
};
