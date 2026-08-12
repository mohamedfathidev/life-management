<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Per-task detail fields: notes, estimated/actual time, and a /10 rating. */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('end_time');
            $table->unsignedSmallInteger('estimated_minutes')->nullable()->after('notes');
            // Override of the actual time; when null we fall back to focus-session totals.
            $table->unsignedSmallInteger('actual_minutes')->nullable()->after('estimated_minutes');
            $table->unsignedTinyInteger('rating')->nullable()->after('actual_minutes'); // 0–10
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['notes', 'estimated_minutes', 'actual_minutes', 'rating']);
        });
    }
};
