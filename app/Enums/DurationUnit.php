<?php

namespace App\Enums;

enum DurationUnit: string
{
    case Months = 'months';
    case Years = 'years';

    public function label(): string
    {
        return match ($this) {
            self::Months => 'شهور',
            self::Years => 'سنين',
        };
    }
}
