<?php

namespace App\Enums;

enum Theme: string
{
    case Light = 'light';
    case Dark = 'dark';
    case System = 'system';

    public function label(): string
    {
        return match ($this) {
            self::Light => 'فاتح',
            self::Dark => 'داكن',
            self::System => 'حسب النظام',
        };
    }
}
