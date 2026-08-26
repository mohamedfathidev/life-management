<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\RecoveryHardMoment>
 */
class RecoveryHardMomentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->randomElement(['السهر لوحدي بالليل', 'بعد شجار أو خناقة', 'وقت الفراغ والملل', 'بعد يوم متعب نفسيًا']),
            'description' => fake()->sentence(),
            'plan' => '<p>'.fake()->sentence().'</p>',
        ];
    }
}
