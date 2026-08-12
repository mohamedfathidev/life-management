<?php

namespace App\Enums;

enum ActivityType: string
{
    case Competition = 'competition'; // مسابقة / هاكاثون
    case Workshop = 'workshop';       // ورشة / تدريب
    case Conference = 'conference';   // مؤتمر / فعالية
    case Volunteering = 'volunteering'; // تطوع / نشاط طلابي
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Competition => 'مسابقة / هاكاثون',
            self::Workshop => 'ورشة / تدريب',
            self::Conference => 'مؤتمر / فعالية',
            self::Volunteering => 'تطوع / نشاط طلابي',
            self::Other => 'أخرى',
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            self::Competition => '🏆',
            self::Workshop => '🛠️',
            self::Conference => '🎤',
            self::Volunteering => '🤝',
            self::Other => '📌',
        };
    }
}
