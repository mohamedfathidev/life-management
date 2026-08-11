<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // null day_id = the task lives in the backlog "pool"
            $table->foreignId('day_id')->nullable()->constrained()->nullOnDelete();
            // optional link to a goal or sub-goal
            $table->foreignId('goal_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->enum('kind', ['goal', 'errand', 'chore', 'challenge', 'recovery', 'other'])
                ->default('other');
            $table->unsignedTinyInteger('progress')->default(0); // 0-100
            $table->enum('status', ['pending', 'in_progress', 'done'])->default('pending');
            $table->unsignedInteger('position')->default(0);
            $table->unsignedSmallInteger('carry_count')->default(0); // times carried over
            $table->timestamps();

            $table->index(['user_id', 'day_id']);
            $table->index(['goal_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
