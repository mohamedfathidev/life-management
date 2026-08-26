<?php

namespace Database\Factories;

use App\Enums\MentalNutritionSourceType;
use App\Models\RecoveryTopic;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MentalNutritionLog>
 */
class MentalNutritionLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'source_type' => MentalNutritionSourceType::Topic,
            'source_id' => RecoveryTopic::factory(),
            'date' => Carbon::today()->toDateString(),
            'reflection' => null,
        ];
    }

    public function on(Carbon|string $date): static
    {
        return $this->state(fn () => [
            'date' => $date instanceof Carbon ? $date->toDateString() : $date,
        ]);
    }

    /** Log a specific source item, e.g. ->ofSource(MentalNutritionSourceType::Damage, $damage->id). */
    public function ofSource(MentalNutritionSourceType $type, int $id): static
    {
        return $this->state(fn () => ['source_type' => $type, 'source_id' => $id]);
    }
}
