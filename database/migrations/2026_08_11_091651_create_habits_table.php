<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('habits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('goal_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->enum('frequency', ['daily', 'weekly'])->default('daily');
            $table->unsignedTinyInteger('weekly_target')->nullable(); // times/week for weekly habits
            $table->string('color', 20)->default('#3F7D7A');
            $table->boolean('is_archived')->default(false);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'is_archived']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('habits');
    }
};
