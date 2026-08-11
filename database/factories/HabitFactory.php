<?php

namespace Database\Factories;

use App\Enums\HabitType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Habit>
 */
class HabitFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'goal_id' => null,
            'title' => fake()->randomElement(['رياضة', 'ماء', 'قراءة', 'نوم مبكر']),
            'type' => HabitType::Recurring->value,
            'start_date' => Carbon::today()->subDays(14)->toDateString(),
            'end_date' => null,
            'color' => '#3F7D7A',
            'is_archived' => false,
            'position' => 0,
        ];
    }

    public function intermittent(string $start, string $end): static
    {
        return $this->state(fn () => [
            'type' => HabitType::Intermittent->value,
            'start_date' => $start,
            'end_date' => $end,
        ]);
    }
}
