<?php

namespace App\Models;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    use HasFactory;

    /** Wallet category used for the mirrored expense of every donation. */
    public const EXPENSE_CATEGORY = 'صدقة';

    protected $fillable = ['user_id', 'transaction_id', 'date', 'amount', 'cause', 'note', 'is_recurring'];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'is_recurring' => 'boolean',
    ];

    /**
     * Keep a mirrored wallet expense in sync with the donation: created on save,
     * updated when its money-relevant fields change, and removed on delete.
     */
    protected static function booted(): void
    {
        static::created(fn (Donation $donation) => $donation->syncExpense());

        static::updated(function (Donation $donation) {
            if ($donation->wasChanged(['amount', 'date', 'cause', 'note'])) {
                $donation->syncExpense();
            }
        });

        static::deleted(fn (Donation $donation) => $donation->transaction()->delete());
    }

    // ---------------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** The mirrored "صدقة" expense in the wallet. */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    // ---------------------------------------------------------------------
    // Behaviour
    // ---------------------------------------------------------------------

    /** Create or update the wallet expense that mirrors this donation. */
    public function syncExpense(): void
    {
        $attributes = [
            'user_id' => $this->user_id,
            'type' => TransactionType::Expense->value,
            'amount' => $this->amount,
            'category' => self::EXPENSE_CATEGORY,
            'note' => $this->cause ?: $this->note,
            'date' => $this->date->toDateString(),
        ];

        if ($this->transaction) {
            $this->transaction->update($attributes);

            return;
        }

        $transaction = Transaction::create($attributes);

        // Link back without firing another round of model events.
        $this->transaction_id = $transaction->id;
        $this->saveQuietly();
    }
}
