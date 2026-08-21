<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** "ليه مبتغيرش؟" — a self-referential tree of root-cause reasons. */
    public function up(): void
    {
        Schema::create('diary_reasons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('diary_reasons')->cascadeOnDelete();
            $table->text('body'); // encrypted
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'parent_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diary_reasons');
    }
};
