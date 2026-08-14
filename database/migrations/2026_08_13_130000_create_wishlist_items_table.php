<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** "Essentials only" — things to buy with an importance level; only affects the
     *  wallet if actually purchased (then a transaction is created). */
    public function up(): void
    {
        Schema::create('wishlist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('transaction_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->decimal('estimated_price', 12, 2)->nullable();
            $table->string('importance')->default('medium'); // critical | high | medium | low
            $table->text('note')->nullable();
            $table->boolean('is_bought')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'is_bought']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wishlist_items');
    }
};
