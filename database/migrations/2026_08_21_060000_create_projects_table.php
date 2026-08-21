<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('goal_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('pitch')->nullable();   // الفكرة في سطرين
            $table->text('why')->nullable();     // ليه عايز تجربها
            $table->string('url')->nullable();   // لينك لما تطلع للنور
            $table->enum('status', ['idea', 'in_progress', 'paused', 'done'])->default('idea');
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
