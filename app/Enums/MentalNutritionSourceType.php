<?php

namespace App\Enums;

/**
 * Where a daily "التغذية الذهنية" item comes from — spans several recovery
 * and diary tabs so the daily suggestion isn't limited to «تعلّم» alone.
 */
enum MentalNutritionSourceType: string
{
    case Topic = 'topic';
    case Damage = 'damage';
    case Dream = 'dream';
    case Change = 'change';
    case Mistake = 'mistake';
    case Story = 'story';
    case Pledge = 'pledge';
    case DiaryReason = 'diary_reason';
    case DiaryChange = 'diary_change';
    case HardMoment = 'hard_moment';
    case Idea = 'idea';
    case Commitment = 'commitment';

    public function label(): string
    {
        return match ($this) {
            self::Topic => 'تعلّم',
            self::Damage => 'أضرار الإدمان',
            self::Dream => 'أحلام التعافي',
            self::Change => 'تغييرات جذرية',
            self::Mistake => 'أخطاء التعافي',
            self::Story => 'حكايات التعافي',
            self::Pledge => 'تعهد أمام الله',
            self::DiaryReason => 'ليه مبتغيرش',
            self::DiaryChange => 'إيه اللي غيّرني',
            self::HardMoment => 'أصعب اللحظات',
            self::Idea => 'أفكار تراودني',
            self::Commitment => 'حاجات لازم تلتزم بيها',
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            self::Topic => '📚',
            self::Damage => '⚠️',
            self::Dream => '✨',
            self::Change => '🧭',
            self::Mistake => '⛓️',
            self::Story => '📖',
            self::Pledge => '🤝',
            self::DiaryReason => '🌳',
            self::DiaryChange => '🌱',
            self::HardMoment => '⚡',
            self::Idea => '💡',
            self::Commitment => '📜',
        };
    }
}
