<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recovery_logs', function (Blueprint $table) {
            $table->string('sleep_location')->nullable()->after('prepared_for_sleep');
            $table->string('sleep_spot')->nullable()->after('sleep_location');
            $table->text('avoidance_reasons')->nullable()->after('sleep_spot');
            $table->text('protection_actions')->nullable()->after('avoidance_reasons');
        });
    }

    public function down(): void
    {
        Schema::table('recovery_logs', function (Blueprint $table) {
            $table->dropColumn(['sleep_location', 'sleep_spot', 'avoidance_reasons', 'protection_actions']);
        });
    }
};
