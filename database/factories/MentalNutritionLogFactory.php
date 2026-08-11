<?php

namespace Database\Factories;

use App\Models\RecoveryTopic;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MentalNutritionLog>
 */
class MentalNutritionLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'recovery_topic_id' => RecoveryTopic::factory(),
            'date' => Carbon::today()->toDateString(),
            'reflection' => null,
        ];
    }

    public function on(Carbon|string $date): static
    {
        return $this->state(fn () => [
            'date' => $date instanceof Carbon ? $date->toDateString() : $date,
        ]);
    }
}
