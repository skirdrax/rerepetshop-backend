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
    Schema::create('pembayaran', function (Blueprint $table) {
        $table->increments('id_pembayaran');
        $table->unsignedInteger('id_pesanan');
        $table->string('metode_bayar', 50)->default('Pakasir');
        $table->enum('status_bayar', ['pending','paid','failed'])->default('pending');
        $table->string('ref_gateway', 100)->nullable();
        $table->dateTime('waktu_bayar')->nullable();
        $table->timestamps();

        $table->unique('id_pesanan');

        $table->foreign('id_pesanan')
              ->references('id_pesanan')
              ->on('pesanan')
              ->onDelete('cascade')
              ->onUpdate('cascade');
    });
}

public function down(): void
{
    Schema::dropIfExists('pembayaran');
}
};
