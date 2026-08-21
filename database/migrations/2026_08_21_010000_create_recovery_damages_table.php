<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** "أضرار الإدمان" — main damages with one level of sub-damages, each weighted 0–100. */
    public function up(): void
    {
        Schema::create('recovery_damages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('recovery_damages')->cascadeOnDelete();
            $table->string('title');
            $table->string('icon', 8)->nullable();
            $table->text('description')->nullable(); // encrypted
            $table->unsignedTinyInteger('degree')->default(0); // 0–100: drives the circle toward red
            $table->json('life_without')->nullable(); // bullets: how life looks if this damage is gone
            $table->timestamps();

            $table->index(['user_id', 'parent_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recovery_damages');
    }
};
