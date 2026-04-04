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
        Schema::create('cpanel_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete(); // Hangi siteye ait
            $table->string('domain');                          // cPanel domain adresi
            $table->string('username');                        // cPanel kullanıcı adı
            $table->text('api_token');                         // API token (model içinde encrypted)
            $table->integer('disk_used_mb')->nullable();       // Kullanılan disk alanı (MB)
            $table->integer('disk_limit_mb')->nullable();      // Toplam disk limiti (MB)
            $table->decimal('disk_usage_percent', 5, 2)->nullable(); // Disk doluluk yüzdesi
            $table->timestamp('last_checked_at')->nullable();  // Son kontrol zamanı
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cpanel_accounts');
    }
};
