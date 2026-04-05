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
        Schema::create('page_speed_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('mobile_score')->nullable();
            $table->unsignedTinyInteger('desktop_score')->nullable();
            $table->unsignedSmallInteger('mobile_fcp')->nullable();       // First Contentful Paint (ms)
            $table->unsignedSmallInteger('desktop_fcp')->nullable();
            $table->unsignedSmallInteger('mobile_lcp')->nullable();       // Largest Contentful Paint (ms)
            $table->unsignedSmallInteger('desktop_lcp')->nullable();
            $table->timestamp('checked_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_speed_scores');
    }
};
