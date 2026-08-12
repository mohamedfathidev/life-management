<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Link each donation to a mirrored wallet expense (category "صدقة"), and
     * backfill existing donations so the wallet reflects all past charity.
     */
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->foreignId('transaction_id')->nullable()->after('user_id')
                ->constrained()->nullOnDelete();
        });

        // Backfill: mirror every existing donation as an expense transaction.
        DB::table('donations')->whereNull('transaction_id')->orderBy('id')
            ->each(function ($donation) {
                $transactionId = DB::table('transactions')->insertGetId([
                    'user_id' => $donation->user_id,
                    'type' => 'expense',
                    'amount' => $donation->amount,
                    'category' => 'صدقة',
                    'note' => $donation->cause ?: $donation->note,
                    'date' => $donation->date,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('donations')->where('id', $donation->id)
                    ->update(['transaction_id' => $transactionId]);
            });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('transaction_id');
        });
    }
};
