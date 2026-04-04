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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete(); // Hangi siteye ait
            $table->enum('type', [                             // Bildirim tipi
                'site_down',           // Site çöktü
                'ssl_expiry',          // SSL süresi doluyor
                'disk_warning',        // Disk doluluk uyarısı
                'update_available',    // Güncelleme mevcut
            ]);
            $table->enum('channel', ['mail', 'slack'])->default('mail'); // Gönderim kanalı
            $table->string('subject');                         // Bildirim konusu
            $table->text('message');                           // Bildirim mesajı
            $table->timestamp('sent_at')->useCurrent();        // Gönderim zamanı
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
