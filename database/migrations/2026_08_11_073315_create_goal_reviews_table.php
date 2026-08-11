<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goal_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('goal_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('improvement_percent')->default(0); // 0-100
            $table->json('shortcomings')->nullable(); // array of strings
            $table->json('strengths')->nullable();    // array of strings
            $table->text('note')->nullable();
            $table->date('closed_on');
            $table->timestamps();

            $table->index(['user_id', 'closed_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goal_reviews');
    }
};
