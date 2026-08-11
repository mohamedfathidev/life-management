<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('goal_id')->nullable()->constrained()->nullOnDelete();
            $table->string('position');
            $table->string('company');
            $table->string('applied_via')->nullable(); // LinkedIn / Wuzzuf / referral …
            $table->string('url')->nullable();
            $table->text('description')->nullable();
            $table->date('applied_on')->nullable();
            $table->enum('stage', ['wishlist', 'applied', 'interview', 'offer', 'rejected'])->default('wishlist');
            $table->text('rejection_reason')->nullable();
            $table->text('company_research')->nullable(); // rich HTML — interview prep
            $table->timestamps();

            $table->index(['user_id', 'stage']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
