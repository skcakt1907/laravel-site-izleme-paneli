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
        Schema::create('ssl_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete(); // Hangi siteye ait
            $table->string('issuer')->nullable();          // Sertifika sağlayıcı (Let's Encrypt vb.)
            $table->date('expires_at');                    // Son kullanma tarihi
            $table->integer('days_remaining')->default(0); // Kalan gün sayısı
            $table->boolean('is_valid')->default(true);    // Sertifika geçerli mi?
            $table->timestamp('last_checked_at')->nullable(); // Son kontrol zamanı
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ssl_certificates');
    }
};
