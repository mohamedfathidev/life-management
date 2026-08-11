<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('goals', function (Blueprint $table) {
            // Self-referencing hierarchy: a goal can contain sub-goals.
            $table->foreignId('parent_id')
                ->nullable()
                ->after('user_id')
                ->constrained('goals')
                ->cascadeOnDelete();

            // start_date pairs with the existing target_date (end date)
            // to drive remaining-days and time-progress calculations.
            $table->date('start_date')->nullable()->after('description');

            $table->index(['parent_id']);
        });
    }

    public function down(): void
    {
        Schema::table('goals', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'start_date']);
        });
    }
};
