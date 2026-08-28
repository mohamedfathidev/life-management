<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('health_harms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('note')->nullable(); // encrypted
            $table->unsignedTinyInteger('severity')->default(50); // 0–100، أد إيه خطورة عليك
            $table->timestamps();

            $table->index(['user_id', 'severity']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_harms');
    }
};
