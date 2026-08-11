<?php

namespace App\Enums;

enum GoalCategory: string
{
    case General = 'general';
    case Health = 'health';
    case Career = 'career';
    case Education = 'education';
    case Religion = 'religion';
    case Personal = 'personal';
    case Finance = 'finance';

    /** Arabic label for the UI. */
    public function label(): string
    {
        return match ($this) {
            self::General => 'عام',
            self::Health => 'صحة',
            self::Career => 'مهنة',
            self::Education => 'تعليم',
            self::Religion => 'دين',
            self::Personal => 'شخصي',
            self::Finance => 'مالية',
        };
    }

    /** Default palette color for the category (hex, matches §9). */
    public function color(): string
    {
        return match ($this) {
            self::General => '#3F7D7A',
            self::Health => '#7A9E7E',
            self::Career => '#5FA6A2',
            self::Education => '#D9A25A',
            self::Religion => '#B9A97C',
            self::Personal => '#C77B7B',
            self::Finance => '#D8C9A3',
        };
    }

    /** @return array<int, array{value:string,label:string,color:string}> */
    public static function options(): array
    {
        return array_map(
            fn (self $c) => ['value' => $c->value, 'label' => $c->label(), 'color' => $c->color()],
            self::cases(),
        );
    }
}
