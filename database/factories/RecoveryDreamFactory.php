<?php

namespace Database\Factories;

use App\Models\RecoveryDream;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RecoveryDream>
 */
class RecoveryDreamFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'recovery_id' => null,
            'icon' => fake()->randomElement(['🌅', '🌱', '🏡', '💪', '🎓', '✈️']),
            'title' => fake()->sentence(4),
            'benefits' => fake()->randomElements([
                'ثقة أكبر بنفسي',
                'وقت وطاقة أكتر لأهلي',
                'تركيز أعلى في شغلي',
                'راحة بال حقيقية',
                'صحة أفضل',
            ], 2),
            'is_achieved' => false,
            'achieved_at' => null,
        ];
    }

    public function achieved(): static
    {
        return $this->state(fn () => [
            'is_achieved' => true,
            'achieved_at' => now()->toDateString(),
        ]);
    }
}
