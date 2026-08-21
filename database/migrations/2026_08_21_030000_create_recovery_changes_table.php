<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recovery_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recovery_id')->nullable()->constrained()->nullOnDelete();
            $table->string('icon', 10)->nullable();
            $table->string('title');
            $table->text('why')->nullable();
            $table->string('status')->default('active');
            $table->date('started_at');
            $table->date('target_date')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recovery_changes');
    }
};
