<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Week;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Week>
 */
class WeekFactory extends Factory
{
    public function definition(): array
    {
        [$start, $end] = Week::boundariesFor(Carbon::today());

        return [
            'user_id' => User::factory(),
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'note' => null,
        ];
    }
}
