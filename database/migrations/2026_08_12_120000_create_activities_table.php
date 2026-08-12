<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Activities & participations: competitions, workshops, conferences, volunteering. */
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('goal_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->enum('type', ['competition', 'workshop', 'conference', 'volunteering', 'other'])->default('other');
            $table->string('organizer')->nullable();
            $table->string('location')->nullable();
            $table->string('url', 2000)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('stage', ['interested', 'applied', 'accepted', 'done', 'rejected'])->default('interested');
            $table->string('result')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'stage']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
