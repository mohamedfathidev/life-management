<?php

namespace App\Enums;

enum TaskStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Done = 'done';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'لم تبدأ',
            self::InProgress => 'قيد التنفيذ',
            self::Done => 'مكتملة',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::InProgress => 'primary',
            self::Done => 'success',
        };
    }

    /** Derive the status from a 0–100 progress value. */
    public static function fromProgress(int $progress): self
    {
        return match (true) {
            $progress >= 100 => self::Done,
            $progress <= 0 => self::Pending,
            default => self::InProgress,
        };
    }
}
