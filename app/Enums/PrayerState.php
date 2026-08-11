<?php

namespace App\Enums;

enum PrayerState: string
{
    case None = 'none';       // لم تُصلَّ
    case Prayed = 'prayed';   // صُلّيت (في غير وقتها)
    case OnTime = 'ontime';   // في وقتها

    public function label(): string
    {
        return match ($this) {
            self::None => 'لم تُصلَّ',
            self::Prayed => 'صُلّيت',
            self::OnTime => 'في وقتها',
        };
    }

    /** Counts as done (whether on time or late). */
    public function isDone(): bool
    {
        return $this !== self::None;
    }

    /** The next state when cycling by tap. */
    public function next(): self
    {
        return match ($this) {
            self::None => self::OnTime,
            self::OnTime => self::Prayed,
            self::Prayed => self::None,
        };
    }
}
