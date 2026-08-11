<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Volunteering gains an application pipeline (same stations as scholarships):
 * some entries are current activities (accepted), others are in-progress applications.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('volunteer_activities', function (Blueprint $table) {
            $table->string('applied_via')->nullable()->after('organization'); // قدّمت من خلال
            $table->enum('stage', ['preparing', 'submitted', 'waiting', 'accepted', 'rejected'])
                ->default('accepted')->after('applied_via');
            $table->date('submitted_on')->nullable()->after('stage');
            $table->date('decided_on')->nullable()->after('submitted_on');
            $table->text('rejection_reason')->nullable()->after('decided_on');
        });
    }

    public function down(): void
    {
        Schema::table('volunteer_activities', function (Blueprint $table) {
            $table->dropColumn(['applied_via', 'stage', 'submitted_on', 'decided_on', 'rejection_reason']);
        });
    }
};
