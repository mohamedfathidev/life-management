<?php

namespace App\Enums;

enum PrayerState: string
{
    case None = 'none';           // لم تُصلَّ
    case Prayed = 'prayed';       // صُلّيت (في غير وقتها)
    case OnTime = 'ontime';       // في وقتها (منفردًا)
    case Congregation = 'jamaah'; // في جماعة

    public function label(): string
    {
        return match ($this) {
            self::None => 'لم تُصلَّ',
            self::Prayed => 'صُلّيت',
            self::OnTime => 'في وقتها',
            self::Congregation => 'جماعة',
        };
    }

    /** Counts as done (whether late, on time, or in congregation). */
    public function isDone(): bool
    {
        return $this !== self::None;
    }

    /** On time whether prayed alone at its time or in congregation. */
    public function isOnTime(): bool
    {
        return $this === self::OnTime || $this === self::Congregation;
    }

    /** Palette color token for the button/badge. */
    public function color(): string
    {
        return match ($this) {
            self::None => 'ink-soft',
            self::Prayed => 'warning',
            self::OnTime => 'success',
            self::Congregation => 'primary',
        };
    }
}
