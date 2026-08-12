<?php

namespace App\Enums;

enum DreamStatus: string
{
    case Dreaming = 'dreaming';   // حلم
    case Pursuing = 'pursuing';   // بسعى له
    case Achieved = 'achieved';   // تحقّق
    case Paused = 'paused';       // متوقّف

    public function label(): string
    {
        return match ($this) {
            self::Dreaming => 'حلم',
            self::Pursuing => 'بسعى له',
            self::Achieved => 'تحقّق',
            self::Paused => 'متوقّف',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Dreaming => 'warning',
            self::Pursuing => 'primary',
            self::Achieved => 'success',
            self::Paused => 'danger',
        };
    }
}
