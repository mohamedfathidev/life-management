<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('study_tracks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('field')->nullable();   // سوفتوير / مانجمنت / تدريس …
            $table->text('plan')->nullable();       // بذاكر إيه
            $table->text('resources')->nullable();  // المصادر المحددة
            $table->text('target')->nullable();     // التارجت
            $table->string('certificate')->nullable(); // لو فيه شهادة
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'is_completed']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_tracks');
    }
};
