<?php

namespace Tests\Feature;

use App\Livewire\Planner\CloseDay;
use App\Livewire\Planner\DayShow;
use App\Livewire\Planner\ManageTask;
use App\Models\Day;
use App\Models\Goal;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PlannerComponentTest extends TestCase
{
    use RefreshDatabase;

    public function test_day_page_renders_and_resolves_a_day(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(DayShow::class, ['date' => '2026-08-11'])
            ->assertOk()
            ->assertViewHas('completion', 0);

        $this->assertTrue(
            Day::where('user_id', $user->id)->whereDate('date', '2026-08-11')->exists()
        );
    }

    public function test_user_cannot_view_another_users_day(): void
    {
        $owner = User::factory()->create();
        $day = Day::factory()->for($owner)->on('2026-08-11')->create();
        $intruder = User::factory()->create();

        Livewire::actingAs($intruder)
            ->test(DayShow::class, ['date' => '2026-08-11'])
            ->assertOk(); // a *new* day is resolved for the intruder, not the owner's

        // the owner's day is untouched and still theirs
        $this->assertSame($owner->id, $day->fresh()->user_id);
    }

    public function test_can_create_a_task_for_a_day(): void
    {
        $user = User::factory()->create();
        $day = Day::factory()->for($user)->create();

        Livewire::actingAs($user)
            ->test(ManageTask::class)
            ->call('openForCreate', dayId: $day->id)
            ->set('form.title', 'مذاكرة')
            ->set('form.kind', 'other')
            ->set('form.progress', 25)
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('task-saved');

        $this->assertDatabaseHas('tasks', [
            'title' => 'مذاكرة',
            'day_id' => $day->id,
            'progress' => 25,
            'status' => 'in_progress',
        ]);
    }

    public function test_task_cannot_link_to_goal_when_day_is_outside_goal_range(): void
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->for($user)->create([
            'start_date' => '2026-08-11',
            'target_date' => '2026-08-13',
        ]);
        $day = Day::factory()->for($user)->on('2026-08-09')->create(); // before the goal starts

        Livewire::actingAs($user)
            ->test(ManageTask::class)
            ->call('openForCreate', dayId: $day->id)
            ->set('form.title', 'مهمة')
            ->set('form.goal_id', $goal->id)
            ->set('form.progress', 0)
            ->call('save')
            ->assertHasErrors(['form.goal_id']);
    }

    public function test_task_can_link_to_goal_when_day_is_within_range(): void
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->for($user)->create([
            'start_date' => '2026-08-11',
            'target_date' => '2026-08-13',
        ]);
        $day = Day::factory()->for($user)->on('2026-08-12')->create(); // inside range

        Livewire::actingAs($user)
            ->test(ManageTask::class)
            ->call('openForCreate', dayId: $day->id)
            ->set('form.title', 'مهمة')
            ->set('form.goal_id', $goal->id)
            ->set('form.progress', 0)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('tasks', ['title' => 'مهمة', 'goal_id' => $goal->id]);
    }

    public function test_updating_task_progress_from_the_day_page(): void
    {
        $user = User::factory()->create();
        $day = Day::factory()->for($user)->create();
        $task = Task::factory()->for($user)->create(['day_id' => $day->id, 'progress' => 0]);

        Livewire::actingAs($user)
            ->test(DayShow::class, ['date' => $day->date->toDateString()])
            ->call('setTaskProgress', $task->id, 100);

        $this->assertSame(100, $task->fresh()->progress);
        $this->assertSame('done', $task->fresh()->status->value);
    }

    public function test_close_day_flow_requires_rating(): void
    {
        $user = User::factory()->create();
        $day = Day::factory()->for($user)->create(['started_at' => now()]);

        Livewire::actingAs($user)
            ->test(CloseDay::class)
            ->call('openFor', $day)
            ->set('rating', null)
            ->call('save')
            ->assertHasErrors(['rating']);
    }

    public function test_close_day_flow_persists_rating_and_reflection(): void
    {
        $user = User::factory()->create();
        $day = Day::factory()->for($user)->create(['started_at' => now()]);

        Livewire::actingAs($user)
            ->test(CloseDay::class)
            ->call('openFor', $day)
            ->set('rating', 7)
            ->set('reflection', 'كان يوم منتج')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('day-updated');

        $day->refresh();
        $this->assertSame('closed', $day->status->value);
        $this->assertSame(7, $day->rating);
        $this->assertSame('كان يوم منتج', $day->reflection);
    }
}
