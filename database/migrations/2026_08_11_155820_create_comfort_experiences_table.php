<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comfort_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->enum('kind', ['first_time', 'hard_challenge'])->default('first_time');
            $table->enum('status', ['wishlist', 'doing', 'done'])->default('wishlist');
            $table->unsignedTinyInteger('difficulty')->nullable(); // 1-5
            $table->text('fear')->nullable();       // اللي حابسني
            $table->text('reflection')->nullable(); // بعد ما أعملها
            $table->date('target_date')->nullable();
            $table->date('done_on')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comfort_experiences');
    }
};
