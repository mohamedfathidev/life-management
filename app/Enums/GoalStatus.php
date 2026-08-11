<?php

namespace App\Enums;

enum GoalStatus: string
{
    case Active = 'active';
    case Paused = 'paused';
    case Completed = 'completed';
    case Abandoned = 'abandoned';

    /** Arabic label for the UI. */
    public function label(): string
    {
        return match ($this) {
            self::Active => 'نشط',
            self::Paused => 'متوقّف مؤقتًا',
            self::Completed => 'مكتمل',
            self::Abandoned => 'متروك',
        };
    }

    /** Tailwind-friendly semantic color token from the palette. */
    public function color(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Paused => 'warning',
            self::Completed => 'primary',
            self::Abandoned => 'danger',
        };
    }

    /** @return array<int, array{value:string,label:string}> */
    public static function options(): array
    {
        return array_map(
            fn (self $c) => ['value' => $c->value, 'label' => $c->label()],
            self::cases(),
        );
    }
}
