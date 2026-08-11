<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scholarships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('institution')->nullable();
            $table->date('apply_from')->nullable();
            $table->date('apply_to')->nullable(); // deadline
            $table->text('requirements')->nullable();
            $table->text('notes')->nullable();
            $table->enum('stage', ['preparing', 'submitted', 'waiting', 'accepted', 'rejected'])->default('preparing');
            $table->date('submitted_on')->nullable();
            $table->date('decided_on')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'stage']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scholarships');
    }
};
