<?php

namespace App\Enums;

enum AppointmentType: string
{
    case Interview = 'interview';
    case Errand = 'errand';
    case Important = 'important';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Interview => 'إنترفيو',
            self::Errand => 'مشوار',
            self::Important => 'مهم',
            self::Other => 'أخرى',
        };
    }

    /** Semantic palette token (safelisted for badges). */
    public function color(): string
    {
        return match ($this) {
            self::Interview => 'primary',
            self::Errand => 'warning',
            self::Important => 'danger',
            self::Other => 'success',
        };
    }

    /** Hex for calendar dots. */
    public function hex(): string
    {
        return match ($this) {
            self::Interview => '#3F7D7A',
            self::Errand => '#D9A25A',
            self::Important => '#C77B7B',
            self::Other => '#7A9E7E',
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            self::Interview => '💼',
            self::Errand => '🚗',
            self::Important => '⭐',
            self::Other => '📌',
        };
    }
}
