<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('week_id')->nullable()->constrained()->nullOnDelete();
            $table->date('date');
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->dateTime('started_at')->nullable(); // actual start of the day
            $table->dateTime('ended_at')->nullable();    // actual end of the day
            $table->unsignedTinyInteger('rating')->nullable(); // 1-10, set on close
            $table->text('reflection')->nullable();            // day review, set on close
            $table->timestamps();

            $table->unique(['user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('days');
    }
};
