<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recovery_stories', function (Blueprint $table) {
            $table->text('brief')->nullable()->after('title'); // encrypted
        });
    }

    public function down(): void
    {
        Schema::table('recovery_stories', function (Blueprint $table) {
            $table->dropColumn('brief');
        });
    }
};
