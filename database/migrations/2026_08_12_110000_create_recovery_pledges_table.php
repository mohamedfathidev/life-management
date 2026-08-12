<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** One personal pledge ("تعهد أمام الله") per user — the commitment text they write. */
    public function up(): void
    {
        Schema::create('recovery_pledges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->text('body')->nullable(); // encrypted at the model layer
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recovery_pledges');
    }
};
