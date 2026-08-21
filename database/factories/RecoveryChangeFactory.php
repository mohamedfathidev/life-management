<?php

namespace Database\Factories;

use App\Enums\RecoveryStatus;
use App\Models\RecoveryChange;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<RecoveryChange>
 */
class RecoveryChangeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'recovery_id' => null,
            'icon' => fake()->randomElement(['🔥', '🧭', '🪞', '🌊', '🛠️']),
            'title' => fake()->sentence(3),
            'why' => fake()->paragraph(),
            'status' => RecoveryStatus::Active,
            'started_at' => Carbon::today()->toDateString(),
            'target_date' => null,
        ];
    }
}
