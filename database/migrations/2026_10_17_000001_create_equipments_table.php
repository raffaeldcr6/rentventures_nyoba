<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipments', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->string('kategori', 50);
            $table->unsignedInteger('harga_sewa_per_hari');
            $table->unsignedInteger('stok')->default(0);
            $table->text('deskripsi')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipments');
    }
};
