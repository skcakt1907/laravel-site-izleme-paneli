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
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->string('name');                    // Müşteri / site adı
            $table->string('url');                     // Site URL'si (https://example.com)
            $table->enum('type', ['wordpress', 'laravel', 'other'])->default('other'); // Site tipi
            $table->string('customer_email')->nullable(); // Müşteri e-posta adresi
            $table->string('hosting_provider')->nullable(); // Hosting sağlayıcı (Turhost, DigitalOcean vb.)
            $table->string('server_ip')->nullable();   // Sunucu IP adresi
            $table->string('php_version')->nullable(); // PHP versiyonu
            $table->text('notes')->nullable();         // Ek notlar
            $table->boolean('is_active')->default(true); // İzleme aktif mi?
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
