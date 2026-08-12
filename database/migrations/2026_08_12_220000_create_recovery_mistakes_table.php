<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** The user's recovery mistakes, each weighted by how much it keeps them stuck. */
    public function up(): void
    {
        Schema::create('recovery_mistakes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('note')->nullable();
            $table->unsignedTinyInteger('weight')->default(50); // 0–100: share of what keeps me in "the prison"
            $table->timestamps();

            $table->index(['user_id', 'weight']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recovery_mistakes');
    }
};
