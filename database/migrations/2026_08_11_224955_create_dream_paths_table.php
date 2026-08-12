<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dream_paths', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dream_id')->constrained()->cascadeOnDelete();
            $table->string('title'); // اسم الطريق: عن طريق الشغل / منحة ماستر …
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dream_paths');
    }
};
