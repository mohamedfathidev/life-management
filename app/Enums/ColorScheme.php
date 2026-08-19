<?php

namespace App\Enums;

enum ColorScheme: string
{
    case Default = 'default';
    case Ocean = 'ocean';
    case Forest = 'forest';
    case Sunset = 'sunset';
    case Lavender = 'lavender';
    case Monochrome = 'monochrome';

    public function label(): string
    {
        return match ($this) {
            self::Default => '🎨 الافتراضي',
            self::Ocean => '🌊 المحيط',
            self::Forest => '🌲 الغابة',
            self::Sunset => '🌅 الغروب',
            self::Lavender => '💜 اللافندر',
            self::Monochrome => '⚫ أحادي اللون',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Default => 'التيل الهادئ مع الرملي',
            self::Ocean => 'أزرق محيطي منعش',
            self::Forest => 'أخضر طبيعي',
            self::Sunset => 'برتقالي ووردي دافئ',
            self::Lavender => 'بنفسجي أنيق',
            self::Monochrome => 'رمادي كلاسيكي',
        };
    }

    /**
     * Get preview colors for this scheme (for UI display)
     * @return array{primary: string, secondary: string, accent: string}
     */
    public function previewColors(): array
    {
        return match ($this) {
            self::Default => [
                'primary' => '#3F7D7A',
                'secondary' => '#D8C9A3',
                'accent' => '#7A9E7E',
            ],
            self::Ocean => [
                'primary' => '#2E5C8A',
                'secondary' => '#7FB3D5',
                'accent' => '#5DADE2',
            ],
            self::Forest => [
                'primary' => '#2D6A4F',
                'secondary' => '#52B788',
                'accent' => '#95D5B2',
            ],
            self::Sunset => [
                'primary' => '#E07A5F',
                'secondary' => '#F4A261',
                'accent' => '#E9C46A',
            ],
            self::Lavender => [
                'primary' => '#9B59B6',
                'secondary' => '#BB8FCE',
                'accent' => '#D7BDE2',
            ],
            self::Monochrome => [
                'primary' => '#4A4A4A',
                'secondary' => '#7A7A7A',
                'accent' => '#A5A5A5',
            ],
        };
    }
}
