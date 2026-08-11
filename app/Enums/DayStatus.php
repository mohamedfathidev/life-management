<?php

namespace App\Enums;

enum DayStatus: string
{
    case Open = 'open';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'مفتوح',
            self::Closed => 'مُقفل',
        };
    }
}
