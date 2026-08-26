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
        Schema::table('mental_nutrition_logs', function (Blueprint $table) {
            $table->string('source_type')->nullable()->after('recovery_topic_id');
            $table->unsignedBigInteger('source_id')->nullable()->after('source_type');
        });

        DB::statement("UPDATE mental_nutrition_logs SET source_type = 'topic', source_id = recovery_topic_id WHERE recovery_topic_id IS NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mental_nutrition_logs', function (Blueprint $table) {
            $table->dropColumn(['source_type', 'source_id']);
        });
    }
};
