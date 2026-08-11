<?php

namespace App\Enums;

enum ScholarshipStage: string
{
    case Preparing = 'preparing';   // تحضير الأوراق
    case Submitted = 'submitted';   // قدّمت الأوراق
    case Waiting = 'waiting';       // انتظار الرد
    case Accepted = 'accepted';     // قبول
    case Rejected = 'rejected';     // رفض

    public function label(): string
    {
        return match ($this) {
            self::Preparing => 'تحضير',
            self::Submitted => 'تقديم الأوراق',
            self::Waiting => 'انتظار الرد',
            self::Accepted => 'قبول',
            self::Rejected => 'رفض',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Preparing => 'warning',
            self::Submitted => 'primary',
            self::Waiting => 'primary',
            self::Accepted => 'success',
            self::Rejected => 'danger',
        };
    }

    /** Ordered stations shown on the timeline. */
    public function order(): int
    {
        return match ($this) {
            self::Preparing => 0,
            self::Submitted => 1,
            self::Waiting => 2,
            self::Accepted, self::Rejected => 3,
        };
    }

    /** The linear pipeline stations (accepted/rejected share the final one). */
    public static function pipeline(): array
    {
        return [self::Preparing, self::Submitted, self::Waiting];
    }

    /**
     * Build the stepper steps for the <x-stepper> component.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function steps(self $current, ?\Illuminate\Support\Carbon $submittedOn = null, ?\Illuminate\Support\Carbon $decidedOn = null): array
    {
        $order = $current->order();
        $steps = [];
        $n = 1;

        foreach (self::pipeline() as $station) {
            $reached = $order >= $station->order();
            $steps[] = [
                'label' => $station->label(),
                'sub' => $station === self::Submitted && $submittedOn ? $submittedOn->translatedFormat('j M') : null,
                'reached' => $reached,
                'mark' => $reached ? '✓' : (string) $n,
                'circleClass' => $reached ? 'bg-primary text-white dark:bg-primary-dark' : 'bg-ink-soft/20 text-ink-soft',
                'lineColor' => 'bg-primary',
            ];
            $n++;
        }

        $steps[] = match ($current) {
            self::Accepted => ['label' => 'قبول', 'sub' => $decidedOn?->translatedFormat('j M'), 'reached' => true, 'mark' => '✓', 'circleClass' => 'bg-success text-white', 'lineColor' => 'bg-success'],
            self::Rejected => ['label' => 'رفض', 'sub' => $decidedOn?->translatedFormat('j M'), 'reached' => true, 'mark' => '✕', 'circleClass' => 'bg-danger text-white', 'lineColor' => 'bg-danger'],
            default => ['label' => 'القرار', 'sub' => null, 'reached' => false, 'mark' => '؟', 'circleClass' => 'bg-ink-soft/20 text-ink-soft', 'lineColor' => 'bg-ink-soft/20'],
        };

        return $steps;
    }
}
