<?php

namespace Tests\Feature;

use App\Livewire\Recovery\Dreams;
use App\Livewire\Recovery\ManageDream;
use App\Models\Recovery;
use App\Models\RecoveryDream;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RecoveryDreamsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_dream_with_benefits(): void
    {
        $user = User::factory()->create();
        $recovery = Recovery::factory()->for($user)->create(['title' => 'الأسبوع الأول']);

        Livewire::actingAs($user)
            ->test(ManageDream::class)
            ->call('openForCreate')
            ->set('form.icon', '🌅')
            ->set('form.title', 'أبقى قدوة لأولادي')
            ->set('form.recovery_id', $recovery->id)
            ->set('form.benefitsInput', "ثقة أكبر بنفسي\nراحة بال حقيقية")
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('dream-saved');

        $dream = RecoveryDream::first();
        $this->assertNotNull($dream);
        $this->assertSame('أبقى قدوة لأولادي', $dream->title);
        $this->assertSame($recovery->id, $dream->recovery_id);
        $this->assertSame(['ثقة أكبر بنفسي', 'راحة بال حقيقية'], $dream->benefits);
        $this->assertFalse($dream->is_achieved);
    }

    public function test_toggling_marks_a_dream_achieved_and_back(): void
    {
        $user = User::factory()->create();
        $dream = RecoveryDream::factory()->for($user)->create(['is_achieved' => false]);

        Livewire::actingAs($user)->test(Dreams::class)->call('toggleAchieved', $dream->id);
        $dream->refresh();
        $this->assertTrue($dream->is_achieved);
        $this->assertNotNull($dream->achieved_at);

        Livewire::actingAs($user)->test(Dreams::class)->call('toggleAchieved', $dream->id);
        $dream->refresh();
        $this->assertFalse($dream->is_achieved);
        $this->assertNull($dream->achieved_at);
    }

    public function test_dreams_index_splits_active_and_achieved(): void
    {
        $user = User::factory()->create();
        RecoveryDream::factory()->for($user)->create(['title' => 'حلم نشط']);
        RecoveryDream::factory()->achieved()->for($user)->create(['title' => 'حلم تحقق']);

        Livewire::actingAs($user)
            ->test(Dreams::class)
            ->assertSee('حلم نشط')
            ->assertSee('حلم تحقق')
            ->assertSee('أحلام اتحققت');
    }

    public function test_user_cannot_edit_another_users_dream(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $dream = RecoveryDream::factory()->for($owner)->create();

        Livewire::actingAs($intruder)
            ->test(ManageDream::class)
            ->call('openForEdit', $dream)
            ->assertForbidden();
    }

    public function test_user_cannot_toggle_another_users_dream(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $dream = RecoveryDream::factory()->for($owner)->create();

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Livewire::actingAs($intruder)
            ->test(Dreams::class)
            ->call('toggleAchieved', $dream->id);
    }
}
