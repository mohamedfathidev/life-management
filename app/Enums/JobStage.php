<?php

namespace App\Enums;

enum JobStage: string
{
    case Wishlist = 'wishlist';   // قائمة رغبات
    case Applied = 'applied';     // قدّمت / انتظار الرد
    case Interview = 'interview'; // إنترفيو
    case Offer = 'offer';         // عرض / قبول
    case Rejected = 'rejected';   // رفض

    public function label(): string
    {
        return match ($this) {
            self::Wishlist => 'قائمة رغبات',
            self::Applied => 'قدّمت',
            self::Interview => 'إنترفيو',
            self::Offer => 'عرض',
            self::Rejected => 'رفض',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Wishlist => 'warning',
            self::Applied => 'primary',
            self::Interview => 'primary',
            self::Offer => 'success',
            self::Rejected => 'danger',
        };
    }

    public function order(): int
    {
        return match ($this) {
            self::Wishlist => 0,
            self::Applied => 1,
            self::Interview => 2,
            self::Offer, self::Rejected => 3,
        };
    }

    /** Linear pipeline stations (offer/rejected share the final one). */
    public static function pipeline(): array
    {
        return [self::Wishlist, self::Applied, self::Interview];
    }

    /**
     * Build the stepper steps for the <x-stepper> component.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function steps(self $current, ?\Illuminate\Support\Carbon $appliedOn = null): array
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
            self::Offer => ['label' => 'عرض', 'sub' => null, 'reached' => true, 'mark' => '✓', 'circleClass' => 'bg-success text-white', 'lineColor' => 'bg-success'],
            self::Rejected => ['label' => 'رفض', 'sub' => null, 'reached' => true, 'mark' => '✕', 'circleClass' => 'bg-danger text-white', 'lineColor' => 'bg-danger'],
            default => ['label' => 'النتيجة', 'sub' => null, 'reached' => false, 'mark' => '؟', 'circleClass' => 'bg-ink-soft/20 text-ink-soft', 'lineColor' => 'bg-ink-soft/20'],
        };

        return $steps;
    }
}
