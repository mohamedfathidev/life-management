<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\RecoveryCommitment>
 */
class RecoveryCommitmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(5),
            'description' => fake()->optional()->sentence(),
            'sort_order' => 0,
        ];
    }
}
