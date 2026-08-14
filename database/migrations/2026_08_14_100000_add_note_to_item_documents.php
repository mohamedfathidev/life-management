<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** A note per checklist item (e.g. how you studied/prepared that step). */
    public function up(): void
    {
        Schema::table('item_documents', function (Blueprint $table) {
            $table->text('note')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('item_documents', function (Blueprint $table) {
            $table->dropColumn('note');
        });
    }
};
