<?php

namespace Tests\Feature;

use App\Enums\GoalStatus;
use App\Livewire\Goals\Index;
use App\Livewire\Goals\ManageGoal;
use App\Livewire\Goals\Show;
use App\Models\Goal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GoalTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_only_the_current_users_goals(): void
    {
        $user = User::factory()->create();
        Goal::factory()->for($user)->create(['title' => 'هدفي']);
        Goal::factory()->create(['title' => 'هدف شخص آخر']);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->assertSee('هدفي')
            ->assertDontSee('هدف شخص آخر');
    }

    public function test_user_can_create_a_goal(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ManageGoal::class)
            ->call('openForCreate')
            ->set('form.title', 'تعلّم البرمجة')
            ->set('form.category', 'education')
            ->set('form.status', 'active')
            ->set('form.color', '#3F7D7A')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('goal-saved');

        $this->assertDatabaseHas('goals', [
            'title' => 'تعلّم البرمجة',
            'user_id' => $user->id,
        ]);
    }

    public function test_goal_title_is_required(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ManageGoal::class)
            ->call('openForCreate')
            ->set('form.title', '')
            ->call('save')
            ->assertHasErrors(['form.title' => 'required']);
    }

    public function test_owner_can_delete_their_goal(): void
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->for($user)->create();

        Livewire::actingAs($user)
            ->test(Index::class)
            ->call('delete', $goal)
            ->assertDispatched('goal-saved');

        $this->assertDatabaseMissing('goals', ['id' => $goal->id]);
    }

    public function test_user_cannot_view_another_users_goal(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $goal = Goal::factory()->for($owner)->create();

        Livewire::actingAs($intruder)
            ->test(Show::class, ['goal' => $goal])
            ->assertForbidden();
    }

    public function test_status_is_cast_to_enum(): void
    {
        $goal = Goal::factory()->status(GoalStatus::Completed)->create();

        $this->assertSame(GoalStatus::Completed, $goal->status);
    }
}
