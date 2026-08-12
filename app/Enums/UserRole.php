<?php

namespace App\Enums;

enum UserRole: string
{
    case Owner = 'owner';             // صاحب التطبيق — يشوف كل حاجة
    case Participant = 'participant';  // مشارك في الساحة فقط

    public function label(): string
    {
        return match ($this) {
            self::Owner => 'المدير',
            self::Participant => 'مشارك',
        };
    }
}
