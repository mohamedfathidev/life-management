<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** Add the "interview" stage to the scholarship/volunteer and activity pipelines. */
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE scholarships MODIFY stage ENUM('preparing','submitted','waiting','interview','accepted','rejected') NOT NULL DEFAULT 'preparing'");
        DB::statement("ALTER TABLE volunteer_activities MODIFY stage ENUM('preparing','submitted','waiting','interview','accepted','rejected') NOT NULL DEFAULT 'preparing'");
        DB::statement("ALTER TABLE activities MODIFY stage ENUM('interested','applied','interview','accepted','done','rejected') NOT NULL DEFAULT 'interested'");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE scholarships MODIFY stage ENUM('preparing','submitted','waiting','accepted','rejected') NOT NULL DEFAULT 'preparing'");
        DB::statement("ALTER TABLE volunteer_activities MODIFY stage ENUM('preparing','submitted','waiting','accepted','rejected') NOT NULL DEFAULT 'preparing'");
        DB::statement("ALTER TABLE activities MODIFY stage ENUM('interested','applied','accepted','done','rejected') NOT NULL DEFAULT 'interested'");
    }
};
