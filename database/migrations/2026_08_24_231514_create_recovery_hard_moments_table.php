<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** "أصعب اللحظات" — recurring trigger scenarios, each paired with a coping plan. */
    public function up(): void
    {
        Schema::create('recovery_hard_moments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable(); // encrypted — إمتى وليه بتحصل اللحظة دي
            $table->text('plan')->nullable(); // encrypted — لما ده يحصل هعمل إيه (Trix)
            $table->timestamps();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recovery_hard_moments');
    }
};
