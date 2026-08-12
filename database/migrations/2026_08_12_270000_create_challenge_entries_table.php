<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** A participant's daily log within a shared challenge (prayers + wird + extras + points). */
    public function up(): void
    {
        Schema::create('challenge_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shared_challenge_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->json('prayers')->nullable();
            $table->unsignedSmallInteger('wird_pages')->default(0);
            $table->json('extras')->nullable();
            $table->unsignedInteger('points')->default(0);
            $table->timestamps();

            $table->unique(['shared_challenge_id', 'user_id', 'date']);
            $table->index(['shared_challenge_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('challenge_entries');
    }
};
