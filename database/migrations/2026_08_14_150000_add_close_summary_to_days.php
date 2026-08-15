<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Snapshot of the real completion at day-close (before carrying tasks over). */
    public function up(): void
    {
        Schema::table('days', function (Blueprint $table) {
            $table->json('close_summary')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('days', function (Blueprint $table) {
            $table->dropColumn('close_summary');
        });
    }
};
