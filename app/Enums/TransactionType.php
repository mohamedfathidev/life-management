<?php

namespace App\Enums;

enum TransactionType: string
{
    case Income = 'income';   // دخل
    case Expense = 'expense'; // مصروف

    public function label(): string
    {
        return match ($this) {
            self::Income => 'دخل',
            self::Expense => 'مصروف',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Income => 'success',
            self::Expense => 'danger',
        };
    }

    public function sign(): int
    {
        return $this === self::Income ? 1 : -1;
    }
}
