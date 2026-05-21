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
    Schema::create('pengiriman', function (Blueprint $table) {
        $table->increments('id_pengiriman');
        $table->unsignedInteger('id_pesanan');
        $table->enum('status_kirim', ['diproses','dikirim','diterima'])->default('diproses');
        $table->string('kurir', 50)->nullable();
        $table->string('resi', 60)->nullable();
        $table->dateTime('tanggal_kirim')->nullable();
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
    Schema::dropIfExists('pengiriman');
}
};
