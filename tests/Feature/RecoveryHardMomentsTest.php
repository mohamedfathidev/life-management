<?php

namespace Tests\Feature;

use App\Livewire\Recovery\HardMomentShow;
use App\Livewire\Recovery\HardMoments;
use App\Models\RecoveryHardMoment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class RecoveryHardMomentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_a_hard_moment(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(HardMoments::class)
            ->set('title', 'السهر لوحدي بالليل')
            ->set('description', 'لما مبنامش بدري وأفضل صاحي لوحدي')
            ->call('save')
            ->assertHasNoErrors();

        $moment = RecoveryHardMoment::first();
        $this->assertNotNull($moment);
        $this->assertSame('السهر لوحدي بالليل', $moment->title);
        $this->assertSame($user->id, $moment->user_id);
    }

    public function test_quick_edit_updates_title_and_description(): void
    {
        $user = User::factory()->create();
        $moment = RecoveryHardMoment::factory()->for($user)->create();

        Livewire::actingAs($user)
            ->test(HardMoments::class)
            ->call('edit', $moment->id)
            ->set('title', 'عنوان معدّل')
            ->set('description', 'وصف معدّل')
            ->call('save')
            ->assertHasNoErrors();

        $moment->refresh();
        $this->assertSame('عنوان معدّل', $moment->title);
        $this->assertSame('وصف معدّل', $moment->description);
    }

    public function test_deleting_from_the_index_removes_it(): void
    {
        $user = User::factory()->create();
        $moment = RecoveryHardMoment::factory()->for($user)->create();

        Livewire::actingAs($user)->test(HardMoments::class)->call('delete', $moment->id);

        $this->assertDatabaseMissing('recovery_hard_moments', ['id' => $moment->id]);
    }

    public function test_user_cannot_quick_edit_another_users_moment(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $moment = RecoveryHardMoment::factory()->for($owner)->create();

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Livewire::actingAs($intruder)->test(HardMoments::class)->call('edit', $moment->id);
    }

    public function test_intruder_delete_from_index_does_not_remove_owners_moment(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $moment = RecoveryHardMoment::factory()->for($owner)->create();

        Livewire::actingAs($intruder)->test(HardMoments::class)->call('delete', $moment->id);

        $this->assertDatabaseHas('recovery_hard_moments', ['id' => $moment->id]);
    }

    public function test_show_page_saves_the_coping_plan(): void
    {
        $user = User::factory()->create();
        $moment = RecoveryHardMoment::factory()->for($user)->create(['plan' => null]);

        Livewire::actingAs($user)
            ->test(HardMomentShow::class, ['moment' => $moment])
            ->set('plan', '<p>هقوم أصلي ركعتين وأكلم حد أثق فيه.</p>')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('savedSuccessfully', true);

        $this->assertSame('<p>هقوم أصلي ركعتين وأكلم حد أثق فيه.</p>', $moment->fresh()->plan);
    }

    public function test_show_page_deletes_the_moment_and_redirects(): void
    {
        $user = User::factory()->create();
        $moment = RecoveryHardMoment::factory()->for($user)->create();

        Livewire::actingAs($user)
            ->test(HardMomentShow::class, ['moment' => $moment])
            ->call('delete')
            ->assertRedirect(route('recovery.hard-moments'));

        $this->assertDatabaseMissing('recovery_hard_moments', ['id' => $moment->id]);
    }

    public function test_intruder_cannot_view_another_users_moment(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $moment = RecoveryHardMoment::factory()->for($owner)->create();

        Livewire::actingAs($intruder)
            ->test(HardMomentShow::class, ['moment' => $moment])
            ->assertForbidden();
    }

    public function test_description_and_plan_are_encrypted_at_rest(): void
    {
        $user = User::factory()->create();
        $moment = RecoveryHardMoment::factory()->for($user)->create([
            'description' => 'سبب حساس جدًا',
            'plan' => '<p>خطة سرية</p>',
        ]);

        $raw = DB::table('recovery_hard_moments')->where('id', $moment->id)->first();

        $this->assertNotSame('سبب حساس جدًا', $raw->description);
        $this->assertNotSame('<p>خطة سرية</p>', $raw->plan);
        $this->assertSame('سبب حساس جدًا', $moment->fresh()->description);
        $this->assertSame('<p>خطة سرية</p>', $moment->fresh()->plan);
    }
}
