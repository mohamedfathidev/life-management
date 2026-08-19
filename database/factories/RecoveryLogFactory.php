<?php

namespace Database\Factories;

use App\Models\Recovery;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RecoveryLog>
 */
class RecoveryLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'recovery_id' => Recovery::factory(),
            'date' => Carbon::today()->toDateString(),
            'urge_level' => fake()->numberBetween(1, 10),
            'mood' => fake()->numberBetween(1, 10),
            'trigger_note' => null,
            'note' => null,
            'is_setback' => false,
            'stayed_up_late' => fake()->boolean(),
            'had_dinner' => fake()->boolean(),
            'prepared_for_sleep' => fake()->boolean(),
        ];
    }

    public function setbackOn(Carbon|string $date): static
    {
        return $this->state(fn () => [
            'is_setback' => true,
            'date' => $date instanceof Carbon ? $date->toDateString() : $date,
        ]);
    }
}
