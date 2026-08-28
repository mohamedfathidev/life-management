<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recovery_road_item_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // Identifies a specific real-data item shown on a road, e.g.
            // "log_trigger:42", "damage:7", "log_protection:19:2" — never
            // touches the underlying record itself.
            $table->string('source_key');
            $table->boolean('hidden')->default(false);
            $table->text('body')->nullable(); // edited replacement text, if any
            $table->timestamps();

            $table->unique(['user_id', 'source_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recovery_road_item_overrides');
    }
};
