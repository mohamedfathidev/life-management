<?php

namespace App\Enums;

enum CareerDreamStatus: string
{
    case Dreaming = 'dreaming';   // لسه حلم
    case Pursuing = 'pursuing';   // بشتغل عليها
    case Achieved = 'achieved';   // تحققت

    public function label(): string
    {
        return match ($this) {
            self::Dreaming => 'لسه حلم',
            self::Pursuing => 'بشتغل عليها',
            self::Achieved => 'تحققت',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Dreaming => 'ink-soft',
            self::Pursuing => 'primary',
            self::Achieved => 'success',
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            self::Dreaming => '💭',
            self::Pursuing => '🚀',
            self::Achieved => '✓',
        };
    }
}
