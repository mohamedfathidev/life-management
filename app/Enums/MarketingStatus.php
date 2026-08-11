<?php

namespace App\Enums;

enum MarketingStatus: string
{
    case Idea = 'idea';
    case Draft = 'draft';
    case Scheduled = 'scheduled';
    case Published = 'published';

    public function label(): string
    {
        return match ($this) {
            self::Idea => 'أفكار',
            self::Draft => 'مسودات',
            self::Scheduled => 'مجدولة',
            self::Published => 'منشورة',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Idea => 'warning',
            self::Draft => 'primary',
            self::Scheduled => 'primary',
            self::Published => 'success',
        };
    }

    /** The next status in the pipeline (null when already published). */
    public function next(): ?self
    {
        return match ($this) {
            self::Idea => self::Draft,
            self::Draft => self::Scheduled,
            self::Scheduled => self::Published,
            self::Published => null,
        };
    }
}
