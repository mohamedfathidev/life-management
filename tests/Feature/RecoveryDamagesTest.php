<?php

namespace Tests\Feature;

use App\Livewire\Recovery\Damages;
use App\Livewire\Recovery\DamageShow;
use App\Livewire\Recovery\ManageDamage;
use App\Models\RecoveryDamage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class RecoveryDamagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_main_damage_with_bullets(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ManageDamage::class)
            ->call('openForCreate')
            ->set('form.title', 'ضرر الجسم')
            ->set('form.icon', '🫀')
            ->set('form.degree', 80)
            ->set('form.description', 'بيأثر على القلب والرئتين')
            ->set('form.lifeWithoutInput', "هنبقى أصحاء\nهوفر فلوسي")
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('damage-saved');

        $damage = RecoveryDamage::first();
        $this->assertSame('ضرر الجسم', $damage->title);
        $this->assertSame(80, $damage->degree);
        $this->assertNull($damage->parent_id);
        $this->assertSame(['هنبقى أصحاء', 'هوفر فلوسي'], $damage->life_without);
    }

    public function test_sub_damage_must_attach_to_a_main_damage_only(): void
    {
        $user = User::factory()->create();
        $main = RecoveryDamage::factory()->for($user)->create(['title' => 'ضرر الجسم']);
        $sub = RecoveryDamage::factory()->for($user)->sub()->create(['parent_id' => $main->id, 'title' => 'ضرر القلب']);

        // A sub-damage cannot become a parent.
        Livewire::actingAs($user)
            ->test(ManageDamage::class)
            ->call('openForCreate')
            ->set('form.title', 'ضرر شرايين')
            ->set('form.parent_id', $sub->id)
            ->set('form.degree', 40)
            ->call('save')
            ->assertHasErrors(['form.parent_id']);

        // Attaching to the main damage works.
        Livewire::actingAs($user)
            ->test(ManageDamage::class)
            ->call('openForCreate')
            ->set('form.title', 'ضرر شرايين')
            ->set('form.parent_id', $main->id)
            ->set('form.degree', 40)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertTrue(RecoveryDamage::where('title', 'ضرر شرايين')->first()->parent()->is($main));
    }

    public function test_damage_description_is_encrypted_at_rest(): void
    {
        $user = User::factory()->create();
        $damage = RecoveryDamage::factory()->for($user)->create(['description' => '<p>سر شخصي</p>']);

        $raw = DB::table('recovery_damages')->where('id', $damage->id)->value('description');

        $this->assertNotSame('<p>سر شخصي</p>', $raw);
        $this->assertSame('<p>سر شخصي</p>', $damage->fresh()->description);
    }

    public function test_user_cannot_view_another_users_damage(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $damage = RecoveryDamage::factory()->for($owner)->create();

        Livewire::actingAs($intruder)
            ->test(DamageShow::class, ['damage' => $damage])
            ->assertForbidden();
    }

    public function test_user_cannot_edit_another_users_damage(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $damage = RecoveryDamage::factory()->for($owner)->create();

        Livewire::actingAs($intruder)
            ->test(ManageDamage::class)
            ->call('openForEdit', $damage)
            ->assertForbidden();
    }

    public function test_index_shows_only_main_damages_with_their_children(): void
    {
        $user = User::factory()->create();
        $main = RecoveryDamage::factory()->for($user)->create(['title' => 'ضرر الجسم', 'degree' => 70]);
        RecoveryDamage::factory()->for($user)->create(['parent_id' => $main->id, 'title' => 'ضرر القلب', 'degree' => 90]);
        RecoveryDamage::factory()->for($user)->create(['title' => 'ضرر النفس', 'degree' => 30]);

        $component = Livewire::actingAs($user)->test(Damages::class);

        $component->assertSee('ضرر الجسم')->assertSee('ضرر النفس');

        // Only main damages are listed at the top level.
        $this->assertSame(2, $component->viewData('damages')->count());
        $this->assertSame('ضرر الجسم', $component->viewData('damages')->getCollection()->first()->title); // ordered by degree desc
    }

    public function test_deleting_a_main_damage_cascades_to_sub_damages(): void
    {
        $user = User::factory()->create();
        $main = RecoveryDamage::factory()->for($user)->create();
        $sub = RecoveryDamage::factory()->for($user)->create(['parent_id' => $main->id]);

        Livewire::actingAs($user)
            ->test(ManageDamage::class)
            ->call('delete', $main);

        $this->assertDatabaseMissing('recovery_damages', ['id' => $main->id]);
        $this->assertDatabaseMissing('recovery_damages', ['id' => $sub->id]);
    }
}
