<?php

namespace App\Models;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'type', 'amount', 'category', 'note', 'date'];

    protected $casts = [
        'type' => TransactionType::class,
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    public function scopeExpenses(Builder $query): Builder
    {
        return $query->where('type', TransactionType::Expense);
    }

    public function scopeIncome(Builder $query): Builder
    {
        return $query->where('type', TransactionType::Income);
    }
}
