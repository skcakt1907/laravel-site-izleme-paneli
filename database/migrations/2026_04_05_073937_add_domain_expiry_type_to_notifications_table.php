<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // MySQL enum'a yeni değer ekle
        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('site_down','ssl_expiry','disk_warning','update_available','domain_expiry') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('site_down','ssl_expiry','disk_warning','update_available') NOT NULL");
    }
};
