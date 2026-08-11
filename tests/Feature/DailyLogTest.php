<?php

namespace Tests\Feature;

use App\Livewire\DailyLogs\ManageLog;
use App\Models\DailyLog;
use App\Models\Goal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DailyLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_standalone_log(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ManageLog::class)
            ->call('openForCreate')
            ->set('form.mood', 7)
            ->set('form.difficulty', 3)
            ->set('form.note', 'يوم جيد')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('log-saved');

        $this->assertDatabaseHas('daily_logs', [
            'user_id' => $user->id,
            'note' => 'يوم جيد',
            'goal_id' => null,
        ]);
    }

    public function test_user_can_create_a_log_attached_to_a_goal(): void
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->for($user)->create();

        Livewire::actingAs($user)
            ->test(ManageLog::class)
            ->call('openForCreate', goalId: $goal->id)
            ->set('form.mood', 5)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame(1, $goal->dailyLogs()->count());
    }

    public function test_mood_must_be_between_1_and_10(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ManageLog::class)
            ->call('openForCreate')
            ->set('form.mood', 99)
            ->call('save')
            ->assertHasErrors(['form.mood']);
    }

    public function test_user_cannot_delete_another_users_log(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $log = DailyLog::factory()->for($owner)->create();

        Livewire::actingAs($intruder)
            ->test(ManageLog::class)
            ->call('delete', $log)
            ->assertForbidden();

        $this->assertDatabaseHas('daily_logs', ['id' => $log->id]);
    }
}
