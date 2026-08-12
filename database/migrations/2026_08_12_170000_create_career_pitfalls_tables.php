<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The user's own added career mistakes / lessons.
        Schema::create('career_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('body')->nullable();
            $table->enum('category', ['general', 'ai'])->default('general');
            $table->boolean('avoided')->default(false);
            $table->timestamps();
        });

        // Flags on the curated pitfalls: "this one applies to me".
        Schema::create('career_pitfall_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('pitfall_key');
            $table->timestamps();

            $table->unique(['user_id', 'pitfall_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('career_pitfall_marks');
        Schema::dropIfExists('career_lessons');
    }
};
