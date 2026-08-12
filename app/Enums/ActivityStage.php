<?php

namespace App\Enums;

use Illuminate\Support\Carbon;

enum ActivityStage: string
{
    case Interested = 'interested'; // مهتم / ناوي أشارك
    case Applied = 'applied';       // قدّمت
    case Interview = 'interview';   // إنترفيو
    case Accepted = 'accepted';     // مقبول / مشارك
    case Done = 'done';             // خلصت المشاركة
    case Rejected = 'rejected';     // معتذر / مرفوض

    public function label(): string
    {
        return match ($this) {
            self::Interested => 'مهتم',
            self::Applied => 'قدّمت',
            self::Interview => 'إنترفيو',
            self::Accepted => 'مقبول / مشارك',
            self::Done => 'خلصت',
            self::Rejected => 'مرفوض',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Interested => 'warning',
            self::Applied => 'primary',
            self::Interview => 'primary',
            self::Accepted => 'primary',
            self::Done => 'success',
            self::Rejected => 'danger',
        };
    }

    public function order(): int
    {
        return match ($this) {
            self::Interested => 0,
            self::Applied => 1,
            self::Interview => 2,
            self::Accepted => 3,
            self::Done, self::Rejected => 4,
        };
    }

    /** The next stage along the happy path, or null at the end. */
    public function next(): ?self
    {
        return match ($this) {
            self::Interested => self::Applied,
            self::Applied => self::Interview,
            self::Interview => self::Accepted,
            self::Accepted => self::Done,
            self::Done, self::Rejected => null,
        };
    }

    /** Linear pipeline stations (done/rejected share the final one). */
    public static function pipeline(): array
    {
        return [self::Interested, self::Applied, self::Interview, self::Accepted];
    }

    /**
     * Build the stepper steps for the <x-stepper> component.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function steps(self $current, ?Carbon $appliedOn = null): array
    {
        $order = $current->order();
        $steps = [];
        $n = 1;

        foreach (self::pipeline() as $station) {
            $reached = $order >= $station->order();
            $steps[] = [
                'label' => $station->label(),
                'sub' => $station === self::Applied && $appliedOn ? $appliedOn->translatedFormat('j M') : null,
                'reached' => $reached,
                'mark' => $reached ? '✓' : (string) $n,
                'circleClass' => $reached ? 'bg-primary text-white dark:bg-primary-dark' : 'bg-ink-soft/20 text-ink-soft',
                'lineColor' => 'bg-primary',
            ];
            $n++;
        }

        $steps[] = match ($current) {
            self::Done => ['label' => 'خلصت', 'sub' => null, 'reached' => true, 'mark' => '✓', 'circleClass' => 'bg-success text-white', 'lineColor' => 'bg-success'],
            self::Rejected => ['label' => 'مرفوض', 'sub' => null, 'reached' => true, 'mark' => '✕', 'circleClass' => 'bg-danger text-white', 'lineColor' => 'bg-danger'],
            default => ['label' => 'النتيجة', 'sub' => null, 'reached' => false, 'mark' => '؟', 'circleClass' => 'bg-ink-soft/20 text-ink-soft', 'lineColor' => 'bg-ink-soft/20'],
        };

        return $steps;
    }
}
