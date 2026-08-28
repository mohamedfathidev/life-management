<?php

namespace App\Enums;

enum RecoveryRoad: string
{
    case Destruction = 'destruction';
    case Salvation = 'salvation';

    public function label(): string
    {
        return match ($this) {
            self::Destruction => 'طريق الهلاك',
            self::Salvation => 'طريق النجاة',
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            self::Destruction => '⚠️',
            self::Salvation => '🌱',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Destruction => 'danger',
            self::Salvation => 'success',
        };
    }

    public function destinationLabel(): string
    {
        return match ($this) {
            self::Destruction => '🕳️ النهاية اللي هتوصلها',
            self::Salvation => '🏁 النهاية اللي هتوصلها',
        };
    }
}
