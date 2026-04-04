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
        Schema::create('wordpress_sites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete(); // Hangi siteye ait
            $table->string('wp_version')->nullable();              // WordPress versiyonu
            $table->string('admin_url')->nullable();               // WP admin panel URL'si
            $table->text('api_key')->nullable();                   // REST API anahtarı (model içinde encrypted)
            $table->integer('plugins_total')->default(0);          // Toplam plugin sayısı
            $table->integer('plugins_update_available')->default(0); // Güncelleme bekleyen plugin
            $table->integer('themes_update_available')->default(0);  // Güncelleme bekleyen tema
            $table->timestamp('last_checked_at')->nullable();      // Son kontrol zamanı
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wordpress_sites');
    }
};
