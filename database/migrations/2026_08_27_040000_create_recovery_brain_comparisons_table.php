<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recovery_brain_comparisons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('point'); // النقطة اللي بتتقارن، مثلاً "الراحة"
            $table->text('addictive_text'); // encrypted — الدماغ الإدماني عايز إيه
            $table->text('normal_text'); // encrypted — دماغي الطبيعية عايزة إيه
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recovery_brain_comparisons');
    }
};
