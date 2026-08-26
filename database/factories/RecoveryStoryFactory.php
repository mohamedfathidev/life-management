<?php

namespace Database\Factories;

use App\Models\RecoveryStory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<RecoveryStory>
 */
class RecoveryStoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'recovery_id' => null,
            'date' => Carbon::today()->toDateString(),
            'title' => fake()->sentence(3),
            'brief' => fake()->optional()->sentence(8),
            'content' => '<p>'.fake()->paragraph().'</p>',
            'mood' => fake()->numberBetween(1, 10),
            'tags' => fake()->randomElements(['الأسبوع', 'أسبوع جديد', 'التغلب', 'أمل'], 2),
            'is_featured' => false,
        ];
    }

    public function featured(): static
    {
        return $this->state(fn () => ['is_featured' => true]);
    }
}
