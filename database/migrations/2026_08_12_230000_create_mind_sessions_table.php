<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Brain-training sessions log ("تنضيف العقل"). */
    public function up(): void
    {
        Schema::create('mind_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('game');
            $table->unsignedSmallInteger('minutes')->default(0);
            $table->date('date');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mind_sessions');
    }
};
