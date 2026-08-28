<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recovery_prison_reflections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('prison_years')->nullable(); // كام سنة في السجن ده
            $table->text('body')->nullable(); // encrypted — إيه اللي كان ممكن يحصل لو ما دخلش
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recovery_prison_reflections');
    }
};
