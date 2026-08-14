<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Allow a resource to be an uploaded image (screenshot / photo) instead of / with a link. */
    public function up(): void
    {
        Schema::table('career_resources', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('url');
            $table->string('url', 2000)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('career_resources', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });
    }
};
