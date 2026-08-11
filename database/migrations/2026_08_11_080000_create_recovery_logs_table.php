<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recovery_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recovery_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->unsignedTinyInteger('urge_level')->nullable(); // 1-10
            $table->unsignedTinyInteger('mood')->nullable();       // 1-10
            $table->text('trigger_note')->nullable();              // encrypted
            $table->text('note')->nullable();                      // encrypted
            $table->boolean('is_setback')->default(false);
            $table->timestamps();

            $table->index(['recovery_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recovery_logs');
    }
};
