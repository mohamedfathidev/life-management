<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Your comment + the feedback you got after an interview / rejection. */
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->text('feedback')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn('feedback');
        });
    }
};
