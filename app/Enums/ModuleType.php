<?php

namespace App\Enums;

/**
 * Identifies which module a reusable record (e.g. a daily log) belongs to.
 * Nullable goal linkage means any module works standalone or inside a goal.
 */
enum ModuleType: string
{
    case General = 'general';
    case Addiction = 'addiction';
    case Recovery = 'recovery';
    case Diary = 'diary';
    case Challenge = 'challenge';

    public function label(): string
    {
        return match ($this) {
            self::General => 'عام',
            self::Addiction => 'التعافي من الإدمان',
            self::Recovery => 'التعافي',
            self::Diary => 'المذكرات',
            self::Challenge => 'التحديات',
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
