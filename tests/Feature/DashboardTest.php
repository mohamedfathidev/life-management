<?php

namespace Tests\Feature;

use App\Enums\GoalStatus;
use App\Livewire\Dashboard\Overview;
use App\Models\DailyLog;
use App\Models\Goal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_renders_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSeeLivewire(Overview::class);
    }

    public function test_guests_are_redirected_from_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_dashboard_counts_active_goals_and_todays_logs(): void
    {
        $user = User::factory()->create();
        Goal::factory()->for($user)->count(2)->create(['status' => GoalStatus::Active->value]);
        Goal::factory()->for($user)->create(['status' => GoalStatus::Paused->value]);
        DailyLog::factory()->for($user)->create(['date' => today()]);
        DailyLog::factory()->for($user)->create(['date' => today()->subDays(3)]);

        Livewire::actingAs($user)
            ->test(Overview::class)
            ->assertViewHas('activeGoals', 2)
            ->assertViewHas('logsToday', 1);
    }
}
