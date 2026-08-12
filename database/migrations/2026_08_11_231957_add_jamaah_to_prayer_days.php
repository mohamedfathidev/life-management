<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Add the "jamaah" (congregation) value to each prayer's enum column.
 * MySQL only; sqlite stores enums as text so no change is needed there.
 */
return new class extends Migration
{
    private array $prayers = ['fajr', 'dhuhr', 'asr', 'maghrib', 'isha'];

    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        foreach ($this->prayers as $p) {
            DB::statement("ALTER TABLE prayer_days MODIFY `{$p}` ENUM('none','prayed','ontime','jamaah') NOT NULL DEFAULT 'none'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        foreach ($this->prayers as $p) {
            // revert jamaah rows to ontime before shrinking the enum
            DB::table('prayer_days')->where($p, 'jamaah')->update([$p => 'ontime']);
            DB::statement("ALTER TABLE prayer_days MODIFY `{$p}` ENUM('none','prayed','ontime') NOT NULL DEFAULT 'none'");
        }
    }
};
