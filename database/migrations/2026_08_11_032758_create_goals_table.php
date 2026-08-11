<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('category')->default('general'); // general/health/career/religion/personal...
            $table->text('description')->nullable();
            $table->string('color', 20)->default('#3F7D7A'); // palette primary accent
            $table->string('icon', 40)->nullable();
            $table->enum('status', ['active', 'paused', 'completed', 'abandoned'])->default('active');
            $table->date('target_date')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goals');
    }
};
