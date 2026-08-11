<?php

namespace Database\Factories;

use App\Enums\DayStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Day>
 */
class DayFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'week_id' => null,
            'date' => Carbon::today()->toDateString(),
            'status' => DayStatus::Open->value,
            'started_at' => null,
            'ended_at' => null,
            'rating' => null,
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
