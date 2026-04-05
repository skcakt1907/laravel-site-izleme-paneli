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
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->string('name');                    // Sunucu adı
            $table->string('whm_host');                // WHM host adresi
            $table->string('whm_user');                // WHM kullanıcı adı
            $table->text('whm_token');                 // WHM API token (encrypted)
            $table->unsignedSmallInteger('whm_port')->default(2087); // WHM portu
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servers');
    }
};
