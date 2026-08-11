<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weeks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('start_date'); // Saturday (week start)
            $table->date('end_date');   // Friday
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'start_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weeks');
    }
};
