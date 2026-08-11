<?php

namespace Tests\Feature;

use App\Livewire\Recovery\ManageLog;
use App\Livewire\Recovery\ManageRecovery;
use App\Livewire\Recovery\ManageTopic;
use App\Livewire\Recovery\Topics;
use App\Models\Recovery;
use App\Models\RecoveryLog;
use App\Models\RecoveryTopic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class RecoveryExtrasTest extends TestCase
{
    use RefreshDatabase;

    public function test_recovery_end_date_cannot_precede_start_date(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ManageRecovery::class)
            ->call('openForCreate')
            ->set('form.title', 'تعافٍ')
            ->set('form.start_date', '2026-08-10')
            ->set('form.end_date', '2026-08-01')
            ->set('form.status', 'active')
            ->call('save')
            ->assertHasErrors(['form.end_date']);
    }

    public function test_recovery_period_end_date_is_saved(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ManageRecovery::class)
            ->call('openForCreate')
            ->set('form.title', 'تعافٍ')
            ->set('form.start_date', '2026-08-01')
            ->set('form.end_date', '2026-09-01')
            ->set('form.status', 'active')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('2026-09-01', Recovery::first()->end_date->toDateString());
    }

    public function test_hardest_period_of_the_day_is_saved(): void
    {
        $user = User::factory()->create();
        $recovery = Recovery::factory()->for($user)->create();

        Livewire::actingAs($user)
            ->test(ManageLog::class)
            ->call('openForCreate', recoveryId: $recovery->id)
            ->set('form.date', '2026-08-11')
            ->set('form.hardest_from', '21:00')
            ->set('form.hardest_to', '23:30')
            ->call('save')
            ->assertHasNoErrors();

        $log = RecoveryLog::first();
        $this->assertSame('21:00', substr($log->hardest_from, 0, 5));
        $this->assertSame('23:30', substr($log->hardest_to, 0, 5));
    }

    public function test_user_can_create_a_learning_topic_with_tags(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ManageTopic::class)
            ->call('openForCreate')
            ->set('form.title', 'أصعب اللحظات')
            ->set('form.content', 'المساء هو الأصعب')
            ->set('form.tagsInput', 'محفزات، مساء، محفزات')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('topic-saved');

        $topic = RecoveryTopic::first();
        $this->assertSame(['محفزات', 'مساء'], $topic->tags); // trimmed + de-duplicated
    }

    public function test_topics_can_be_filtered_by_tag(): void
    {
        $user = User::factory()->create();
        RecoveryTopic::factory()->for($user)->withTags(['مساء'])->create(['title' => 'موضوع المساء']);
        RecoveryTopic::factory()->for($user)->withTags(['صباح'])->create(['title' => 'موضوع الصباح']);

        Livewire::actingAs($user)
            ->test(Topics::class)
            ->set('tag', 'مساء')
            ->assertSee('موضوع المساء')
            ->assertDontSee('موضوع الصباح');
    }

    public function test_topic_content_is_encrypted_at_rest(): void
    {
        $user = User::factory()->create();
        $topic = RecoveryTopic::factory()->for($user)->create(['content' => 'سر شخصي']);

        $raw = DB::table('recovery_topics')->where('id', $topic->id)->value('content');

        $this->assertNotSame('سر شخصي', $raw);
        $this->assertSame('سر شخصي', $topic->fresh()->content);
    }

    public function test_user_cannot_edit_another_users_topic(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $topic = RecoveryTopic::factory()->for($owner)->create();

        Livewire::actingAs($intruder)
            ->test(ManageTopic::class)
            ->call('openForEdit', $topic)
            ->assertForbidden();
    }
}
