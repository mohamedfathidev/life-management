<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recovery_change_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recovery_change_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->boolean('is_done')->default(false);
            $table->date('done_at')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['recovery_change_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recovery_change_steps');
    }
};
