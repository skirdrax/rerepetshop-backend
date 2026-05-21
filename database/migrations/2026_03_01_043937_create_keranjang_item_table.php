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
    Schema::create('keranjang', function (Blueprint $table) {
        $table->increments('id_keranjang');
        $table->unsignedInteger('id_pelanggan');
        $table->decimal('total', 12, 2)->default(0);
        $table->timestamps();

        $table->unique('id_pelanggan');

        $table->foreign('id_pelanggan')
              ->references('id_pelanggan')
              ->on('pelanggan')
              ->onDelete('cascade')
              ->onUpdate('cascade');
    });
}

public function down(): void
{
    Schema::dropIfExists('keranjang');
}
};
