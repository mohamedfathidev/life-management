<?php

namespace Database\Factories;

use App\Models\DiaryReason;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DiaryReason>
 */
class DiaryReasonFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'parent_id' => null,
            'body' => fake()->sentence(10),
            'sort_order' => 0,
        ];
    }
}
