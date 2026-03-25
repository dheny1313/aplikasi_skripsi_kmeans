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
        Schema::create('calculation_result', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calculation_log_id')->constrained('calculation_logs')->onDelete('cascade');
            $table->integer('cluster_number'); // Masuk ke Cluster 0, 1, 2, dst.

            // Kolom krusial untuk menyimpan keadaan absolut (Nama, Nilai Kriteria saat kalkulasi)
            $table->json('snapshot_data');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calculation_result');
    }
};
