<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** A library of links/resources (scholarships & jobs) to browse. */
    public function up(): void
    {
        Schema::create('career_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('context')->default('scholarship'); // scholarship | job
            $table->string('title');
            $table->string('url', 2000);
            $table->string('type')->default('website');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'context']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('career_resources');
    }
};
