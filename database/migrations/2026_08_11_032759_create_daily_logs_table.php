<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('goal_id')->nullable()->constrained()->nullOnDelete();
            // reusable across modules: addiction/recovery/diary/challenge/general
            $table->enum('module_type', ['general', 'addiction', 'recovery', 'diary', 'challenge'])
                ->default('general');
            $table->date('date');
            $table->unsignedTinyInteger('mood')->nullable();       // 1-10
            $table->unsignedTinyInteger('difficulty')->nullable(); // 1-10
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'date']);
            $table->index(['goal_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_logs');
    }
};
