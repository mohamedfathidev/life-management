<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\User;
use App\Support\Achievements;

class AchievementService
{
    /**
     * Evaluate every definition for the user, unlock newly-earned ones,
     * and return the full display state.
     *
     * @return array{items: array<int, array<string, mixed>>, new: array<int, array<string, mixed>>, earnedCount: int, total: int}
     */
    public static function sync(User $user): array
    {
        $defs = Achievements::all();
        $unlocked = Achievement::query()->where('user_id', $user->id)->pluck('unlocked_at', 'key');

        $items = [];
        $new = [];

        foreach ($defs as $def) {
            $raw = (int) ($def['current'])($user);
            $wasUnlocked = $unlocked->has($def['key']);
            $earned = $wasUnlocked || $raw >= $def['target'];

            if (! $wasUnlocked && $raw >= $def['target']) {
                $row = Achievement::create([
                    'user_id' => $user->id,
                    'key' => $def['key'],
                    'unlocked_at' => now(),
                ]);
                $unlocked[$def['key']] = $row->unlocked_at;
                $new[] = $def;
            }

            $items[] = [
                'def' => $def,
                'current' => min($raw, $def['target']),
                'earned' => $earned,
                'unlockedAt' => $unlocked->get($def['key']),
                'percent' => $def['target'] > 0 ? min(100, (int) round($raw / $def['target'] * 100)) : 0,
            ];
        }

        return [
            'items' => $items,
            'new' => $new,
            'earnedCount' => collect($items)->where('earned', true)->count(),
            'total' => count($defs),
        ];
    }
}
