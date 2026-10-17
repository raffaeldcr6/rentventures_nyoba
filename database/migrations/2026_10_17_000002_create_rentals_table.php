<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            // restrictOnDelete: alat yang punya riwayat sewa tidak bisa dihapus begitu saja
            $table->foreignId('equipment_id')->constrained('equipments')->restrictOnDelete();
            $table->unsignedInteger('jumlah')->default(1);
            $table->date('tgl_sewa');
            $table->date('tgl_kembali');
            $table->unsignedBigInteger('total_harga');
            $table->enum('status', ['pending', 'disetujui', 'selesai', 'dibatalkan'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};
