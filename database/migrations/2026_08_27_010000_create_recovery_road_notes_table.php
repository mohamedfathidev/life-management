<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recovery_road_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('road'); // 'destruction' | 'salvation'
            $table->string('stage'); // 'start' | 'harvest'
            $table->text('body');
            $table->timestamps();

            $table->index(['user_id', 'road', 'stage']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recovery_road_notes');
    }
};
