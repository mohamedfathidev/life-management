<?php

namespace App\Enums;

enum ExperienceStatus: string
{
    case Wishlist = 'wishlist'; // عايز أجرّبها
    case Doing = 'doing';       // بجرّبها دلوقتي
    case Done = 'done';         // تمّت

    public function label(): string
    {
        return match ($this) {
            self::Wishlist => 'عايز أجرّبها',
            self::Doing => 'بجرّبها',
            self::Done => 'تمّت',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Wishlist => 'warning',
            self::Doing => 'primary',
            self::Done => 'success',
        };
    }
}
