<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mental_nutrition_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recovery_topic_id')->nullable()->constrained()->nullOnDelete();
            $table->date('date');
            $table->text('reflection')->nullable(); // encrypted
            $table->timestamps();

            $table->unique(['user_id', 'date']); // one topic consumed per day
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mental_nutrition_logs');
    }
};
