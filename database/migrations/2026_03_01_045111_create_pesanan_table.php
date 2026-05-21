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
    Schema::create('pesanan', function (Blueprint $table) {
        $table->increments('id_pesanan');
        $table->unsignedInteger('id_pelanggan');
        $table->dateTime('tanggal_pesanan')->useCurrent();
        $table->text('alamat_kirim')->nullable();
        $table->decimal('total', 12, 2)->default(0);
        $table->enum('status_pesanan', ['baru','diproses','dikirim','selesai','batal'])->default('baru');
        $table->timestamps();

        $table->foreign('id_pelanggan')
              ->references('id_pelanggan')
              ->on('pelanggan')
              ->onUpdate('cascade');
    });
}

public function down(): void
{
    Schema::dropIfExists('pesanan');
}
};
