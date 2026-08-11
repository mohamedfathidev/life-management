<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recovery_topics', function (Blueprint $table) {
            $table->enum('importance', ['low', 'medium', 'high'])->default('medium')->after('tags');
        });
    }

    public function down(): void
    {
        Schema::table('recovery_topics', function (Blueprint $table) {
            $table->dropColumn('importance');
        });
    }
};
