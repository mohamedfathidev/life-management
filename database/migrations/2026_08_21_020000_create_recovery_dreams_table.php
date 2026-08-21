<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recovery_dreams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recovery_id')->nullable()->constrained()->nullOnDelete();
            $table->string('icon', 10)->nullable();
            $table->string('title');
            $table->json('benefits')->nullable();
            $table->boolean('is_achieved')->default(false);
            $table->date('achieved_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_achieved']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recovery_dreams');
    }
};
