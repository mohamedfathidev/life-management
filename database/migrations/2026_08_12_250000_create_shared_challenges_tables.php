<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shared_challenges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('join_code', 12)->unique();
            $table->json('scoring'); // points config for prayers / wird / extras
            $table->timestamps();
        });

        Schema::create('shared_challenge_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shared_challenge_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['shared_challenge_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shared_challenge_user');
        Schema::dropIfExists('shared_challenges');
    }
};
