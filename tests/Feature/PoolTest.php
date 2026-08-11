<?php

namespace Tests\Feature;

use App\Livewire\Planner\Pool;
use App\Models\Goal;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class PoolTest extends TestCase
{
    use RefreshDatabase;

    public function test_pool_lists_only_unassigned_tasks(): void
    {
        $user = User::factory()->create();
        Task::factory()->for($user)->create(['day_id' => null, 'title' => 'POOLITEM_AAA']);
        // an assigned task should not show
        $day = \App\Models\Day::factory()->for($user)->create();
        Task::factory()->for($user)->create(['day_id' => $day->id, 'title' => 'ASSIGNED_BBB']);

        Livewire::actingAs($user)
            ->test(Pool::class)
            ->assertSee('POOLITEM_AAA')
            ->assertDontSee('ASSIGNED_BBB');
    }

    public function test_assign_moves_a_pooled_task_to_the_chosen_day(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create(['day_id' => null]);

        Livewire::actingAs($user)
            ->test(Pool::class)
            ->set('assignDate', '2026-08-20')
            ->call('assign', $task->id)
            ->assertHasNoErrors();

        $task->refresh();
        $this->assertNotNull($task->day_id);
        $this->assertSame('2026-08-20', $task->day->date->toDateString());
    }

    public function test_assign_is_blocked_when_outside_linked_goal_range(): void
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->for($user)->create([
            'start_date' => '2026-08-11',
            'target_date' => '2026-08-13',
        ]);
        $task = Task::factory()->for($user)->create(['day_id' => null, 'goal_id' => $goal->id]);

        Livewire::actingAs($user)
            ->test(Pool::class)
            ->set('assignDate', '2026-08-20') // after the goal ends
            ->call('assign', $task->id)
            ->assertHasErrors('assignDate');

        $this->assertNull($task->fresh()->day_id); // not moved
    }

    public function test_goal_accepts_date_within_window(): void
    {
        $goal = Goal::factory()->create([
            'start_date' => '2026-08-11',
            'target_date' => '2026-08-13',
        ]);

        $this->assertTrue($goal->acceptsDate(Carbon::parse('2026-08-12')));
        $this->assertFalse($goal->acceptsDate(Carbon::parse('2026-08-10')));
        $this->assertFalse($goal->acceptsDate(Carbon::parse('2026-08-14')));
    }
}
