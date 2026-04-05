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
        Schema::table('sites', function (Blueprint $table) {
            $table->unsignedTinyInteger('pagespeed_mobile')->nullable()->after('domain_checked_at');
            $table->unsignedTinyInteger('pagespeed_desktop')->nullable()->after('pagespeed_mobile');
            $table->timestamp('pagespeed_checked_at')->nullable()->after('pagespeed_desktop');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn(['pagespeed_mobile', 'pagespeed_desktop', 'pagespeed_checked_at']);
        });
    }
};
