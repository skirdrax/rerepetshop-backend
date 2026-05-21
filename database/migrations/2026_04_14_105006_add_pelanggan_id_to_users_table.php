<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        // Hapus baris role karena sudah ada
        $table->unsignedInteger('pelanggan_id')->nullable()->after('role');
        $table->foreign('pelanggan_id')
              ->references('id_pelanggan')
              ->on('pelanggan')
              ->nullOnDelete();
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropForeign(['pelanggan_id']);
        $table->dropColumn('pelanggan_id'); // Hapus role dari sini juga
    });
}
};