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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->nullable()->constrained()->cascadeOnDelete(); // Site bazlı (null = genel rapor)
            $table->string('period');                          // Rapor dönemi (ör: 2026-04)
            $table->string('file_path');                       // PDF dosya yolu
            $table->timestamp('generated_at')->useCurrent();   // Oluşturulma zamanı
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
