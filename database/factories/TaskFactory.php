<?php

namespace Database\Factories;

use App\Enums\TaskKind;
use App\Enums\TaskStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'day_id' => null,
            'goal_id' => null,
            'title' => fake()->sentence(3),
            'kind' => TaskKind::Other->value,
            'progress' => 0,
            'status' => TaskStatus::Pending->value,
            'position' => 0,
            'carry_count' => 0,
        ];
    }

    public function progress(int $progress): static
    {
        return $this->state(fn () => [
            'progress' => $progress,
            'status' => TaskStatus::fromProgress($progress)->value,
        ]);
    }
}
