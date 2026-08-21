<?php

namespace Tests\Feature;

use App\Livewire\Recovery\ChangeShow;
use App\Livewire\Recovery\Changes;
use App\Livewire\Recovery\ManageChange;
use App\Models\RecoveryChange;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RecoveryChangesTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_change(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ManageChange::class)
            ->call('openForCreate')
            ->set('form.icon', '🔥')
            ->set('form.title', 'أتحكم في انفعالي')
            ->set('form.started_at', '2026-08-21')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('change-saved');

        $change = RecoveryChange::first();
        $this->assertNotNull($change);
        $this->assertSame('أتحكم في انفعالي', $change->title);
        $this->assertSame('active', $change->status->value);
    }

    public function test_user_can_add_and_toggle_steps_and_progress_updates(): void
    {
        $user = User::factory()->create();
        $change = RecoveryChange::factory()->for($user)->create();

        $component = Livewire::actingAs($user)->test(ChangeShow::class, ['change' => $change]);

        $component->set('newStep', 'خطوة أولى')->call('addStep')->assertHasNoErrors();
        $component->set('newStep', 'خطوة تانية')->call('addStep')->assertHasNoErrors();

        $this->assertSame(0, $change->progressPercent());

        $step = $change->steps()->first();
        $component->call('toggleStep', $step->id);

        $this->assertSame(50, $change->progressPercent());
        $this->assertTrue($step->fresh()->is_done);

        $component->call('toggleStep', $step->id);
        $this->assertFalse($step->fresh()->is_done);
    }

    public function test_deleting_a_step_removes_it(): void
    {
        $user = User::factory()->create();
        $change = RecoveryChange::factory()->for($user)->create();
        $step = $change->steps()->create(['title' => 'خطوة', 'sort_order' => 1]);

        Livewire::actingAs($user)
            ->test(ChangeShow::class, ['change' => $change])
            ->call('deleteStep', $step->id);

        $this->assertDatabaseMissing('recovery_change_steps', ['id' => $step->id]);
    }

    public function test_user_cannot_view_or_edit_another_users_change(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $change = RecoveryChange::factory()->for($owner)->create();

        Livewire::actingAs($intruder)
            ->test(ChangeShow::class, ['change' => $change])
            ->assertForbidden();

        Livewire::actingAs($intruder)
            ->test(ManageChange::class)
            ->call('openForEdit', $change)
            ->assertForbidden();
    }

    public function test_changes_index_splits_active_and_completed(): void
    {
        $user = User::factory()->create();
        RecoveryChange::factory()->for($user)->create(['title' => 'تغيير شغال']);
        RecoveryChange::factory()->for($user)->create(['title' => 'تغيير خلص', 'status' => 'completed']);

        Livewire::actingAs($user)
            ->test(Changes::class)
            ->assertSee('تغيير شغال')
            ->assertSee('تغيير خلص')
            ->assertSee('تغييرات اتحققت');
    }
}
