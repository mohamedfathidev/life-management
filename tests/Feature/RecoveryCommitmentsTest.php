<?php

namespace Tests\Feature;

use App\Livewire\Recovery\Commitments;
use App\Models\RecoveryCommitment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class RecoveryCommitmentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_a_commitment(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Commitments::class)
            ->set('title', 'ملهاش موبايل بعد العشاء')
            ->set('description', 'أقفله وأسيبه بره الأوضة')
            ->call('save')
            ->assertHasNoErrors();

        $commitment = RecoveryCommitment::first();
        $this->assertNotNull($commitment);
        $this->assertSame('ملهاش موبايل بعد العشاء', $commitment->title);
        $this->assertSame($user->id, $commitment->user_id);
    }

    public function test_new_commitments_are_appended_after_existing_ones(): void
    {
        $user = User::factory()->create();
        RecoveryCommitment::factory()->for($user)->create(['sort_order' => 3]);

        Livewire::actingAs($user)
            ->test(Commitments::class)
            ->set('title', 'التزام تاني')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame(4, RecoveryCommitment::where('title', 'التزام تاني')->value('sort_order'));
    }

    public function test_editing_updates_title_and_description(): void
    {
        $user = User::factory()->create();
        $commitment = RecoveryCommitment::factory()->for($user)->create();

        Livewire::actingAs($user)
            ->test(Commitments::class)
            ->call('edit', $commitment->id)
            ->set('title', 'عنوان معدّل')
            ->set('description', 'وصف معدّل')
            ->call('save')
            ->assertHasNoErrors();

        $commitment->refresh();
        $this->assertSame('عنوان معدّل', $commitment->title);
        $this->assertSame('وصف معدّل', $commitment->description);
    }

    public function test_deleting_removes_the_commitment(): void
    {
        $user = User::factory()->create();
        $commitment = RecoveryCommitment::factory()->for($user)->create();

        Livewire::actingAs($user)->test(Commitments::class)->call('delete', $commitment->id);

        $this->assertDatabaseMissing('recovery_commitments', ['id' => $commitment->id]);
    }

    public function test_user_cannot_edit_or_delete_another_users_commitment(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $commitment = RecoveryCommitment::factory()->for($owner)->create();

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Livewire::actingAs($intruder)->test(Commitments::class)->call('edit', $commitment->id);
    }

    public function test_intruder_delete_does_not_remove_owners_commitment(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $commitment = RecoveryCommitment::factory()->for($owner)->create();

        Livewire::actingAs($intruder)->test(Commitments::class)->call('delete', $commitment->id);

        $this->assertDatabaseHas('recovery_commitments', ['id' => $commitment->id]);
    }

    public function test_description_is_encrypted_at_rest(): void
    {
        $user = User::factory()->create();
        $commitment = RecoveryCommitment::factory()->for($user)->create(['description' => 'سر شخصي جدًا']);

        $raw = DB::table('recovery_commitments')->where('id', $commitment->id)->value('description');

        $this->assertNotSame('سر شخصي جدًا', $raw);
        $this->assertSame('سر شخصي جدًا', $commitment->fresh()->description);
    }
}
