<?php

namespace Tests\Feature;

use App\Enums\GoalStatus;
use App\Models\Goal;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class GoalCompletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_completed_leaf_goal_is_100_percent(): void
    {
        $goal = Goal::factory()->status(GoalStatus::Completed)->create();

        $this->assertSame(100, $goal->completionPercent());
    }

    public function test_leaf_goal_completion_comes_from_its_tasks(): void
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->for($user)->create(['status' => GoalStatus::Active->value]);
        Task::factory()->for($user)->progress(100)->create(['goal_id' => $goal->id]);
        Task::factory()->for($user)->progress(40)->create(['goal_id' => $goal->id]);

        // (100 + 40) / 2 = 70
        $this->assertSame(70, $goal->completionPercent());
    }

    public function test_parent_goal_averages_its_sub_goals(): void
    {
        $user = User::factory()->create();
        $parent = Goal::factory()->for($user)->create();

        // sub-goal A: completed → 100
        Goal::factory()->childOf($parent)->status(GoalStatus::Completed)->create();
        // sub-goal B: active with a 50%-progress task
        $subB = Goal::factory()->childOf($parent)->create(['status' => GoalStatus::Active->value]);
        Task::factory()->for($user)->progress(50)->create(['goal_id' => $subB->id]);

        // (100 + 50) / 2 = 75
        $this->assertSame(75, $parent->completionPercent());
    }

    public function test_remaining_weeks_rounds_up(): void
    {
        $goal = Goal::factory()->create(['target_date' => Carbon::today()->addDays(10)]);

        // 10 days → 2 weeks (ceil)
        $this->assertSame(2, $goal->remainingWeeks());
    }
}
