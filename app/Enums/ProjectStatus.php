<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case Idea = 'idea';
    case InProgress = 'in_progress';
    case Paused = 'paused';
    case Done = 'done';

    public function label(): string
    {
        return match ($this) {
            self::Idea => 'فكرة',
            self::InProgress => 'بشتغل عليه',
            self::Paused => 'متوقف',
            self::Done => 'خلصته',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Idea => 'warning',
            self::InProgress => 'primary',
            self::Paused => 'danger',
            self::Done => 'success',
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            self::Idea => '💡',
            self::InProgress => '⚙️',
            self::Paused => '⏸️',
            self::Done => '🏁',
        };
    }

    public function order(): int
    {
        return match ($this) {
            self::Idea => 0,
            self::InProgress => 1,
            self::Done, self::Paused => 2,
        };
    }

    /** Linear pipeline stations (done/paused share the final one). */
    public static function pipeline(): array
    {
        return [self::Idea, self::InProgress];
    }

    /**
     * Build the stepper steps for the <x-stepper> component.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function steps(self $current): array
    {
        $order = $current->order();
        $steps = [];
        $n = 1;

        foreach (self::pipeline() as $station) {
            $reached = $order >= $station->order();
            $steps[] = [
                'label' => $station->label(),
                'sub' => null,
                'reached' => $reached,
                'mark' => $reached ? '✓' : (string) $n,
                'circleClass' => $reached ? 'bg-primary text-white dark:bg-primary-dark' : 'bg-ink-soft/20 text-ink-soft',
                'lineColor' => 'bg-primary',
            ];
            $n++;
        }

        $steps[] = match ($current) {
            self::Done => ['label' => 'خلصته', 'sub' => null, 'reached' => true, 'mark' => '✓', 'circleClass' => 'bg-success text-white', 'lineColor' => 'bg-success'],
            self::Paused => ['label' => 'متوقف', 'sub' => null, 'reached' => true, 'mark' => '⏸', 'circleClass' => 'bg-danger text-white', 'lineColor' => 'bg-danger'],
            default => ['label' => 'خلصته', 'sub' => null, 'reached' => false, 'mark' => '؟', 'circleClass' => 'bg-ink-soft/20 text-ink-soft', 'lineColor' => 'bg-ink-soft/20'],
        };

        return $steps;
    }
}
