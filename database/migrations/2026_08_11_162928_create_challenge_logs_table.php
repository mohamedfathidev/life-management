<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('challenge_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('challenge_id')->constrained()->cascadeOnDelete();
            $table->date('date'); // presence of a row = succeeded that day
            $table->timestamps();

            $table->unique(['challenge_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('challenge_logs');
    }
};
