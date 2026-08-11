<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RecoveryTopic>
 */
class RecoveryTopicFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'content' => fake()->paragraph(),
            'tags' => [],
        ];
    }

    /** @param array<int, string> $tags */
    public function withTags(array $tags): static
    {
        return $this->state(fn () => ['tags' => $tags]);
    }
}
