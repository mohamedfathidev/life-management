<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Allow nested checklist items (a step with sub-steps). */
    public function up(): void
    {
        Schema::table('item_documents', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->after('id')
                ->constrained('item_documents')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('item_documents', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parent_id');
        });
    }
};
