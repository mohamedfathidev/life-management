<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Track whether the day was "done" (fed mind + prepared to sleep) or "missed" (stayed up). */
    public function up(): void
    {
        Schema::table('night_checks', function (Blueprint $table) {
            $table->string('status')->default('done')->after('date');
        });

        DB::table('night_checks')->update(['status' => 'done']);
    }

    public function down(): void
    {
        Schema::table('night_checks', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
