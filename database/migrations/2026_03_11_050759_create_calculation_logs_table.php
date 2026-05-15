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
        Schema::create('calculation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // Admin/Teacher yang memproses
            $table->integer('k_value'); // Jumlah cluster yang dibentuk
            $table->float('dbi_score')->nullable(); // Nilai evaluasi Davies-Bouldin Index
            $table->integer('total_iterations')->nullable(); // Berapa iterasi hingga konvergen
            $table->string('description')->nullable(); // Misal: "Evaluasi Semester Ganjil 2026"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calculation_logs');
    }
};
