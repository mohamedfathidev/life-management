<?php

namespace App\Enums;

enum HabitType: string
{
    case Recurring = 'recurring';     // ongoing from start_date, no end
    case Intermittent = 'intermittent'; // bounded to a start_date → end_date period

    public function label(): string
    {
        return match ($this) {
            self::Recurring => 'متكررة',
            self::Intermittent => 'متقطعة',
        };
    }
}
