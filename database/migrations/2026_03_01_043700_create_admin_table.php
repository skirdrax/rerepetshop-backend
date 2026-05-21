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
    Schema::create('admin', function (Blueprint $table) {
        $table->increments('id_admin');
        $table->string('nama', 100);
        $table->string('email', 120)->unique();
        $table->string('password', 255);
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('admin');
}
};
