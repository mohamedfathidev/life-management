<?php

namespace App\Enums;

enum RecoveryStatus: string
{
    case Active = 'active';
    case Paused = 'paused';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'نشط',
            self::Paused => 'متوقّف مؤقتًا',
            self::Completed => 'مكتمل',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Paused => 'warning',
            self::Completed => 'primary',
        };
    }
}
