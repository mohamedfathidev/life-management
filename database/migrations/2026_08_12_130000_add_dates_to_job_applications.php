<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Track the application deadline and the interview day for each job. */
    public function up(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->date('deadline')->nullable()->after('applied_on');    // آخر موعد تقديم
            $table->date('interview_at')->nullable()->after('deadline');  // يوم الإنترفيو
        });
    }

    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn(['deadline', 'interview_at']);
        });
    }
};
