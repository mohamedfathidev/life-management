<?php

namespace Tests\Feature;

use App\Livewire\Recovery\Ideas;
use App\Models\RecoveryIdea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class RecoveryIdeasTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_an_idea(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Ideas::class)
            ->set('body', 'ممكن أعمل روتين صباحي ثابت')
            ->call('save')
            ->assertHasNoErrors();

        $idea = RecoveryIdea::first();
        $this->assertNotNull($idea);
        $this->assertSame('ممكن أعمل روتين صباحي ثابت', $idea->body);
        $this->assertSame($user->id, $idea->user_id);
    }

    public function test_user_can_add_an_idea_with_action_taken(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Ideas::class)
            ->set('body', 'ممكن أعمل روتين صباحي ثابت')
            ->set('actionTaken', 'بدأت النهارده بالفعل')
            ->call('save')
            ->assertHasNoErrors();

        $idea = RecoveryIdea::first();
        $this->assertSame('بدأت النهارده بالفعل', $idea->action_taken);
    }

    public function test_editing_updates_the_body(): void
    {
        $user = User::factory()->create();
        $idea = RecoveryIdea::factory()->for($user)->create();

        Livewire::actingAs($user)
            ->test(Ideas::class)
            ->call('edit', $idea->id)
            ->set('body', 'نص معدّل')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('نص معدّل', $idea->fresh()->body);
    }

    public function test_deleting_removes_the_idea(): void
    {
        $user = User::factory()->create();
        $idea = RecoveryIdea::factory()->for($user)->create();

        Livewire::actingAs($user)->test(Ideas::class)->call('delete', $idea->id);

        $this->assertDatabaseMissing('recovery_ideas', ['id' => $idea->id]);
    }

    public function test_user_cannot_edit_or_delete_another_users_idea(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $idea = RecoveryIdea::factory()->for($owner)->create();

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Livewire::actingAs($intruder)->test(Ideas::class)->call('edit', $idea->id);
    }

    public function test_intruder_delete_does_not_remove_owners_idea(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $idea = RecoveryIdea::factory()->for($owner)->create();

        Livewire::actingAs($intruder)->test(Ideas::class)->call('delete', $idea->id);

        $this->assertDatabaseHas('recovery_ideas', ['id' => $idea->id]);
    }

    public function test_body_is_encrypted_at_rest(): void
    {
        $user = User::factory()->create();
        $idea = RecoveryIdea::factory()->for($user)->create(['body' => 'سر شخصي جدًا']);

        $raw = DB::table('recovery_ideas')->where('id', $idea->id)->value('body');

        $this->assertNotSame('سر شخصي جدًا', $raw);
        $this->assertSame('سر شخصي جدًا', $idea->fresh()->body);
    }

    public function test_action_taken_is_encrypted_at_rest(): void
    {
        $user = User::factory()->create();
        $idea = RecoveryIdea::factory()->for($user)->create(['action_taken' => 'تعامل سري']);

        $raw = DB::table('recovery_ideas')->where('id', $idea->id)->value('action_taken');

        $this->assertNotSame('تعامل سري', $raw);
        $this->assertSame('تعامل سري', $idea->fresh()->action_taken);
    }
}
