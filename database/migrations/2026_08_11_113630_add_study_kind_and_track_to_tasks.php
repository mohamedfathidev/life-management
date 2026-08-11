<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('study_track_id')->nullable()->after('goal_id')
                ->constrained()->nullOnDelete();
        });

        // Add the new "study" value to the kind enum (MySQL only; sqlite stores it as text).
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE tasks MODIFY kind ENUM('goal','study','errand','chore','challenge','recovery','other') NOT NULL DEFAULT 'other'");
        }
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('study_track_id');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE tasks MODIFY kind ENUM('goal','errand','chore','challenge','recovery','other') NOT NULL DEFAULT 'other'");
        }
    }
};
