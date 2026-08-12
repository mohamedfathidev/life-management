<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dreams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('why')->nullable();            // ليه بحلم بيه
            $table->string('from_point')->nullable();    // أنا واقف فين
            $table->string('to_point')->nullable();      // عايز أوصل فين
            $table->unsignedSmallInteger('duration_value')->nullable();
            $table->enum('duration_unit', ['months', 'years'])->default('years');
            $table->date('target_date')->nullable();
            $table->enum('status', ['dreaming', 'pursuing', 'achieved', 'paused'])->default('dreaming');
            $table->string('color', 20)->default('#3F7D7A');
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dreams');
    }
};
