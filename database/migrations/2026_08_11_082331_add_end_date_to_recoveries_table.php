<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recoveries', function (Blueprint $table) {
            // The recovery period runs from start_date to end_date (nullable = open-ended).
            $table->date('end_date')->nullable()->after('start_date');
        });
    }

    public function down(): void
    {
        Schema::table('recoveries', function (Blueprint $table) {
            $table->dropColumn('end_date');
        });
    }
};
