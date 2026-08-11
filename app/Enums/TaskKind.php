<?php

namespace App\Enums;

/**
 * Lightweight categorization for a task when it isn't tied to a goal.
 * Independent of the heavier modules (challenge/recovery) which arrive later.
 */
enum TaskKind: string
{
    case Goal = 'goal';
    case Study = 'study';
    case Errand = 'errand';
    case Chore = 'chore';
    case Challenge = 'challenge';
    case Recovery = 'recovery';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Goal => 'هدف',
            self::Study => 'مذاكرة',
            self::Errand => 'مشوار',
            self::Chore => 'مصلحة',
            self::Challenge => 'تحدٍّ',
            self::Recovery => 'تعافٍ',
            self::Other => 'أخرى',
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            self::Goal => '🎯',
            self::Study => '📚',
            self::Errand => '🚗',
            self::Chore => '🧾',
            self::Challenge => '🔥',
            self::Recovery => '🌱',
            self::Other => '📌',
        };
    }

    /** @return array<int, array{value:string,label:string}> */
    public static function options(): array
    {
        return array_map(
            fn (self $c) => ['value' => $c->value, 'label' => $c->emoji().' '.$c->label()],
            self::cases(),
        );
    }
}
