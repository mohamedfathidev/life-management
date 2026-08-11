<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('platform')->default('LinkedIn');
            $table->string('topic');
            $table->text('content')->nullable();
            $table->enum('status', ['idea', 'draft', 'scheduled', 'published'])->default('idea');
            $table->date('scheduled_for')->nullable();
            $table->string('link')->nullable(); // link to the published post
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_posts');
    }
};
