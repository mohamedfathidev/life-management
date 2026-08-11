<?php

namespace App\Enums;

enum ExperienceKind: string
{
    case FirstTime = 'first_time';       // أول مرة أجرّبها
    case HardChallenge = 'hard_challenge'; // تحدٍّ صعب

    public function label(): string
    {
        return match ($this) {
            self::FirstTime => 'أول مرة',
            self::HardChallenge => 'تحدٍّ صعب',
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            self::FirstTime => '🌱',
            self::HardChallenge => '🔥',
        };
    }
}
