<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Reshape habits: drop the weekly-frequency model in favour of a type
 * (recurring / intermittent) with an explicit period (start_date → end_date).
 * All habits are now daily-tracked.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('habits', function (Blueprint $table) {
            $table->enum('type', ['recurring', 'intermittent'])->default('recurring')->after('title');
            $table->date('start_date')->nullable()->after('type');
            $table->date('end_date')->nullable()->after('start_date');
        });

        // Backfill start_date for any pre-existing habits.
        DB::table('habits')->whereNull('start_date')
            ->update(['start_date' => DB::raw('date(created_at)')]);

        Schema::table('habits', function (Blueprint $table) {
            $table->dropColumn(['frequency', 'weekly_target']);
        });
    }

    public function down(): void
    {
        Schema::table('habits', function (Blueprint $table) {
            $table->enum('frequency', ['daily', 'weekly'])->default('daily')->after('title');
            $table->unsignedTinyInteger('weekly_target')->nullable()->after('frequency');
            $table->dropColumn(['type', 'start_date', 'end_date']);
        });
    }
};
