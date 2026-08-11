<?php

namespace Database\Factories;

use App\Models\Habit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HabitLog>
 */
class HabitLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'habit_id' => Habit::factory(),
            'date' => Carbon::today()->toDateString(),
        ];
    }

    public function on(Carbon|string $date): static
    {
        return $this->state(fn () => [
            'date' => $date instanceof Carbon ? $date->toDateString() : $date,
        ]);
    }
}
