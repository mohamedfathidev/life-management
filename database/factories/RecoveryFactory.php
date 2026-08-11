<?php

namespace Database\Factories;

use App\Enums\RecoveryStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Recovery>
 */
class RecoveryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'goal_id' => null,
            'title' => fake()->sentence(2),
            'description' => null,
            'start_date' => Carbon::today()->subDays(30)->toDateString(),
            'status' => RecoveryStatus::Active->value,
        ];
    }

    public function startedDaysAgo(int $days): static
    {
        return $this->state(fn () => [
            'start_date' => Carbon::today()->subDays($days)->toDateString(),
        ]);
    }
}
