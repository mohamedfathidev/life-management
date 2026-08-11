<?php

namespace App\Enums;

enum ChallengeStatus: string
{
    case Active = 'active';
    case Completed = 'completed';
    case Abandoned = 'abandoned';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'جارٍ',
            self::Completed => 'مكتمل',
            self::Abandoned => 'متروك',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'primary',
            self::Completed => 'success',
            self::Abandoned => 'danger',
        };
    }
}
