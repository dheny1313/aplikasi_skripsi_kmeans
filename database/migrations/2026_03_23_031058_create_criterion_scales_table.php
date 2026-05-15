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
        Schema::create('criterion_scales', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel criteria. Jika kriteria dihapus, skalanya ikut terhapus (cascade)
            $table->foreignId('criterion_id')->constrained('criteria')->onDelete('cascade');
            $table->integer('scale_value'); // Angka 1, 2, 3, 4, 5
            $table->string('description'); // Deskripsi spesifik, cth: "Sering bolos tanpa keterangan"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('criterion_scales');
    }
};
