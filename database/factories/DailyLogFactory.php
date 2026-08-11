<?php

namespace Database\Factories;

use App\Enums\ModuleType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DailyLog>
 */
class DailyLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'goal_id' => null,
            'module_type' => ModuleType::General->value,
            'date' => fake()->dateTimeBetween('-7 days', 'now')->format('Y-m-d'),
            'mood' => fake()->numberBetween(1, 10),
            'difficulty' => fake()->numberBetween(1, 10),
            'note' => fake()->optional()->sentence(),
        ];
    }
}
