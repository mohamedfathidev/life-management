<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quran_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('from_surah')->nullable();
            $table->unsignedSmallInteger('from_ayah')->nullable();
            $table->string('to_surah')->nullable();
            $table->unsignedSmallInteger('to_ayah')->nullable();
            $table->unsignedSmallInteger('pages')->default(0); // pages read this session
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quran_logs');
    }
};
