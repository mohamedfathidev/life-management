<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\RecoveryMistake>
 */
class RecoveryMistakeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(4),
            'note' => '<p>'.fake()->paragraph().'</p>',
            'weight' => fake()->numberBetween(0, 100),
        ];
    }
}
