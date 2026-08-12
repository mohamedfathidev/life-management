<?php

namespace App\Enums;

/**
 * Categorizes a task by its type / originating module, so a task collected from
 * any part of the app (career, dreams, worship, habits, study…) still shows a
 * meaningful badge inside today's plan and the unified task hub.
 */
enum TaskKind: string
{
    case Goal = 'goal';
    case Study = 'study';
    case Career = 'career';
    case Habit = 'habit';
    case Dream = 'dream';
    case Worship = 'worship';
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
            self::Career => 'كارير',
            self::Habit => 'عادة',
            self::Dream => 'حلم',
            self::Worship => 'عبادة',
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
            self::Career => '💼',
            self::Habit => '🔁',
            self::Dream => '✨',
            self::Worship => '🕌',
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
