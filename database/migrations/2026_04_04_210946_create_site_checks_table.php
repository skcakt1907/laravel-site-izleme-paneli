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
        Schema::create('site_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete(); // Hangi siteye ait
            $table->smallInteger('status_code')->nullable();    // HTTP durum kodu (200, 500 vb.)
            $table->integer('response_time_ms')->nullable();    // Yanıt süresi (milisaniye)
            $table->boolean('is_up')->default(false);           // Site açık mı?
            $table->integer('consecutive_failures')->default(0); // Ard arda başarısız kontrol sayısı
            $table->text('error_message')->nullable();          // Hata varsa mesaj
            $table->timestamp('checked_at')->useCurrent();      // Kontrol zamanı
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_checks');
    }
};
