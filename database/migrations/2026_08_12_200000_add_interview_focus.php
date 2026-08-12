<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** "What to focus on for the interview" note, for any career item in the interview stage. */
    public function up(): void
    {
        foreach (['activities', 'volunteer_activities', 'scholarships', 'job_applications'] as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->text('interview_focus')->nullable();
            });
        }
    }

    public function down(): void
    {
        foreach (['activities', 'volunteer_activities', 'scholarships', 'job_applications'] as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropColumn('interview_focus');
            });
        }
    }
};
