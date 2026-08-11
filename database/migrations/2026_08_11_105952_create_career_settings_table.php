<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('career_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->text('study_motto')->nullable(); // شعار تحفيزي يكتبه المستخدم
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('career_settings');
    }
};
