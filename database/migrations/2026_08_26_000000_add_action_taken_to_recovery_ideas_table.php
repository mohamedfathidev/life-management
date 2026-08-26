<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recovery_ideas', function (Blueprint $table) {
            $table->text('action_taken')->nullable()->after('body'); // encrypted
        });
    }

    public function down(): void
    {
        Schema::table('recovery_ideas', function (Blueprint $table) {
            $table->dropColumn('action_taken');
        });
    }
};
