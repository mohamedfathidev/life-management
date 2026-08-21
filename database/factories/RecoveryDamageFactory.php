<?php

namespace Database\Factories;

use App\Models\RecoveryDamage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RecoveryDamage>
 */
class RecoveryDamageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'parent_id' => null,
            'title' => fake()->randomElement(['ضرر الجسم', 'ضرر النفس', 'ضرر المال', 'ضرر العلاقات']),
            'icon' => fake()->randomElements(['🫀', '🧠', '💸', '💔'], 1)[0],
            'description' => '<p>'.fake()->sentence().'</p>',
            'degree' => fake()->numberBetween(10, 100),
            'life_without' => fake()->sentences(2),
        ];
    }

    public function sub(): static
    {
        return $this->state(fn () => [
            'parent_id' => RecoveryDamage::factory(),
        ]);
    }
}
