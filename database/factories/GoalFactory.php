<?php

namespace Database\Factories;

use App\Enums\GoalCategory;
use App\Enums\GoalStatus;
use App\Models\Goal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Goal>
 */
class GoalFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'parent_id' => null,
            'title' => fake()->sentence(3),
            'category' => fake()->randomElement(GoalCategory::cases())->value,
            'description' => fake()->optional()->paragraph(),
            'color' => fake()->hexColor(),
            'icon' => null,
            'status' => GoalStatus::Active->value,
            'start_date' => null,
            'target_date' => fake()->optional()->dateTimeBetween('now', '+1 year'),
        ];
    }

    public function status(GoalStatus $status): static
    {
        return $this->state(fn () => ['status' => $status->value]);
    }

    /** A sub-goal nested under the given parent goal. */
    public function childOf(Goal $parent): static
    {
        return $this->state(fn () => [
            'parent_id' => $parent->id,
            'user_id' => $parent->user_id,
        ]);
    }
}
