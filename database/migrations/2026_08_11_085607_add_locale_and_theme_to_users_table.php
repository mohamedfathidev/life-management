<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Backfill for databases whose users table was created before locale/theme
 * were added to the base migration. Conditional so it's a no-op on fresh DBs.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'locale')) {
                $table->string('locale', 5)->default('ar')->after('password');
            }
            if (! Schema::hasColumn('users', 'theme')) {
                $table->enum('theme', ['light', 'dark', 'system'])->default('system')->after('locale');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach (['locale', 'theme'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
