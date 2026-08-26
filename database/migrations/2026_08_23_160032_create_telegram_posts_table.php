<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('telegram_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('channel');
            $table->unsignedBigInteger('message_id');
            $table->text('content')->nullable();
            $table->text('image_url')->nullable(); // Telegram's signed CDN URLs run well past 255 chars
            $table->text('video_url')->nullable();
            $table->text('post_url');
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'channel', 'message_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telegram_posts');
    }
};
