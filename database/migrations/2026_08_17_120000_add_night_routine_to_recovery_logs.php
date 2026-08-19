<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recovery_logs', function (Blueprint $table) {
            $table->boolean('stayed_up_late')->nullable()->after('is_setback');
            $table->boolean('had_dinner')->nullable()->after('stayed_up_late');
            $table->boolean('prepared_for_sleep')->nullable()->after('had_dinner');
        });
    }

    public function down(): void
    {
        Schema::table('recovery_logs', function (Blueprint $table) {
            $table->dropColumn(['stayed_up_late', 'had_dinner', 'prepared_for_sleep']);
        });
    }
};
