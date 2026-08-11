<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recovery_logs', function (Blueprint $table) {
            // The hardest stretch of the day (from → to).
            $table->time('hardest_from')->nullable()->after('is_setback');
            $table->time('hardest_to')->nullable()->after('hardest_from');
        });
    }

    public function down(): void
    {
        Schema::table('recovery_logs', function (Blueprint $table) {
            $table->dropColumn(['hardest_from', 'hardest_to']);
        });
    }
};
