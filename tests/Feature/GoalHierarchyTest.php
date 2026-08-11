<?php

namespace Tests\Feature;

use App\Enums\GoalStatus;
use App\Livewire\Goals\Index;
use App\Livewire\Goals\ManageGoal;
use App\Livewire\Goals\Show;
use App\Models\Goal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class GoalHierarchyTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_shows_only_top_level_goals(): void
    {
        $user = User::factory()->create();
        $parent = Goal::factory()->for($user)->create(['title' => 'الهدف الكبير']);
        Goal::factory()->childOf($parent)->create(['title' => 'خطوة فرعية']);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->assertSee('الهدف الكبير')
            ->assertDontSee('خطوة فرعية');
    }

    public function test_can_create_a_sub_goal_under_a_parent(): void
    {
        $user = User::factory()->create();
        $parent = Goal::factory()->for($user)->create();

        Livewire::actingAs($user)
            ->test(ManageGoal::class)
            ->call('openForCreate', parentId: $parent->id)
            ->set('form.title', 'هدف فرعي')
            ->set('form.category', 'general')
            ->set('form.status', 'active')
            ->set('form.color', '#3F7D7A')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('goals', [
            'title' => 'هدف فرعي',
            'parent_id' => $parent->id,
        ]);
        $this->assertSame(1, $parent->children()->count());
    }

    public function test_detail_page_lists_sub_goals(): void
    {
        $user = User::factory()->create();
        $parent = Goal::factory()->for($user)->create();
        Goal::factory()->childOf($parent)->create(['title' => 'خطوة أولى']);

        Livewire::actingAs($user)
            ->test(Show::class, ['goal' => $parent])
            ->assertViewHas('children', fn ($children) => $children->count() === 1)
            ->assertSee('خطوة أولى');
    }

    public function test_deleting_parent_cascades_to_sub_goals(): void
    {
        $user = User::factory()->create();
        $parent = Goal::factory()->for($user)->create();
        $child = Goal::factory()->childOf($parent)->create();

        $parent->delete();

        $this->assertDatabaseMissing('goals', ['id' => $child->id]);
    }

    public function test_remaining_days_is_positive_before_deadline(): void
    {
        $goal = Goal::factory()->create([
            'target_date' => Carbon::today()->addDays(10),
        ]);

        $this->assertSame(10, $goal->remainingDays());
        $this->assertFalse($goal->isOverdue());
    }

    public function test_goal_is_overdue_after_deadline(): void
    {
        $goal = Goal::factory()->create([
            'status' => GoalStatus::Active->value,
            'target_date' => Carbon::today()->subDays(3),
        ]);

        $this->assertSame(-3, $goal->remainingDays());
        $this->assertTrue($goal->isOverdue());
    }

    public function test_time_progress_is_halfway_through_the_window(): void
    {
        $goal = Goal::factory()->create([
            'start_date' => Carbon::today()->subDays(5),
            'target_date' => Carbon::today()->addDays(5),
        ]);

        $this->assertSame(50, $goal->timeProgressPercent());
    }

    public function test_children_progress_reflects_completed_sub_goals(): void
    {
        $parent = Goal::factory()->create();
        Goal::factory()->childOf($parent)->status(GoalStatus::Completed)->create();
        Goal::factory()->childOf($parent)->status(GoalStatus::Active)->create();

        $this->assertSame(50, $parent->childrenProgressPercent());
    }

    public function test_end_date_must_not_precede_start_date(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ManageGoal::class)
            ->call('openForCreate')
            ->set('form.title', 'هدف')
            ->set('form.start_date', Carbon::today()->addDays(5)->toDateString())
            ->set('form.target_date', Carbon::today()->toDateString())
            ->call('save')
            ->assertHasErrors(['form.target_date']);
    }
}
