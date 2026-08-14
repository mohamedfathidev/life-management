<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Optionally link a per-item document to a document in the general library. */
    public function up(): void
    {
        Schema::table('item_documents', function (Blueprint $table) {
            $table->foreignId('scholarship_document_id')->nullable()->after('user_id')
                ->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('item_documents', function (Blueprint $table) {
            $table->dropConstrainedForeignId('scholarship_document_id');
        });
    }
};
