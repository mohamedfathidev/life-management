<?php

namespace Tests\Feature;

use App\Livewire\Recovery\ManageStory;
use App\Livewire\Recovery\Stories;
use App\Livewire\Recovery\StoryShow;
use App\Models\Recovery;
use App\Models\RecoveryStory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class RecoveryStoriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_recovery_story_linked_to_a_period(): void
    {
        $user = User::factory()->create();
        $recovery = Recovery::factory()->for($user)->create(['title' => 'الأسبوع الثاني']);

        Livewire::actingAs($user)
            ->test(ManageStory::class)
            ->call('openForCreate', recoveryId: $recovery->id)
            ->assertSet('form.recovery_id', $recovery->id)
            ->set('form.date', '2026-08-21')
            ->set('form.title', 'أول حكاية')
            ->set('form.content', '<p>حكايتي الأولى في التعافي</p>')
            ->set('form.tagsInput', '#أسبوعي_الأول #أمل')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('story-saved');

        $story = RecoveryStory::first();
        $this->assertNotNull($story);
        $this->assertSame('أول حكاية', $story->title);
        $this->assertSame($recovery->id, $story->recovery_id);
        $this->assertSame(['أسبوعي_الأول', 'أمل'], $story->tags);
    }

    public function test_user_can_save_a_brief_for_a_story(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ManageStory::class)
            ->call('openForCreate')
            ->set('form.date', '2026-08-21')
            ->set('form.title', 'حكاية')
            ->set('form.brief', 'الحكاية دي عن أول مرة قاومت فيها الرغبة')
            ->set('form.content', '<p>تفاصيل الحكاية</p>')
            ->call('save')
            ->assertHasNoErrors();

        $story = RecoveryStory::first();
        $this->assertSame('الحكاية دي عن أول مرة قاومت فيها الرغبة', $story->brief);
    }

    public function test_brief_is_encrypted_at_rest(): void
    {
        $user = User::factory()->create();
        $story = RecoveryStory::factory()->for($user)->create(['brief' => 'ملخص سري']);

        $raw = DB::table('recovery_stories')->where('id', $story->id)->value('brief');

        $this->assertNotSame('ملخص سري', $raw);
        $this->assertSame('ملخص سري', $story->fresh()->brief);
    }

    public function test_story_content_is_encrypted_at_rest(): void
    {
        $user = User::factory()->create();
        $story = RecoveryStory::factory()->for($user)->create(['content' => '<p>سر شخصي</p>']);

        $raw = DB::table('recovery_stories')->where('id', $story->id)->value('content');

        $this->assertNotSame('<p>سر شخصي</p>', $raw);
        $this->assertSame('<p>سر شخصي</p>', $story->fresh()->content);
    }

    public function test_user_cannot_view_another_users_story(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $story = RecoveryStory::factory()->for($owner)->create();

        Livewire::actingAs($intruder)
            ->test(StoryShow::class, ['story' => $story])
            ->assertForbidden();
    }

    public function test_user_cannot_edit_another_users_story(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $story = RecoveryStory::factory()->for($owner)->create();

        Livewire::actingAs($intruder)
            ->test(ManageStory::class)
            ->call('openForEdit', $story)
            ->assertForbidden();
    }

    public function test_stories_index_filters_by_linked_recovery(): void
    {
        $user = User::factory()->create();
        $recovery = Recovery::factory()->for($user)->create(['title' => 'فترة الشتاء']);
        RecoveryStory::factory()->for($user)->create(['recovery_id' => $recovery->id, 'title' => 'حكاية الأسبوع']);
        RecoveryStory::factory()->for($user)->create(['recovery_id' => null, 'title' => 'حكاية عامة']);

        Livewire::actingAs($user)
            ->test(Stories::class)
            ->assertSee('كل فترات التعافي')
            ->assertSee('فترة الشتاء') // the period picker lists user's periods
            ->set('recoveryId', $recovery->id)
            ->assertSee('حكاية الأسبوع')
            ->assertDontSee('حكاية عامة');
    }

    public function test_user_can_toggle_a_story_as_featured(): void
    {
        $user = User::factory()->create();
        $story = RecoveryStory::factory()->for($user)->create();

        Livewire::actingAs($user)
            ->test(Stories::class)
            ->call('toggleFeatured', $story->id);

        $this->assertTrue($story->fresh()->is_featured);

        Livewire::actingAs($user)
            ->test(Stories::class)
            ->call('toggleFeatured', $story->id);

        $this->assertFalse($story->fresh()->is_featured);
    }

    public function test_featured_stories_are_listed_first(): void
    {
        $user = User::factory()->create();
        RecoveryStory::factory()->for($user)->create(['title' => 'حكاية عادية', 'date' => '2026-08-20']);
        $featured = RecoveryStory::factory()->featured()->for($user)->create(['title' => 'حكاية مميزة', 'date' => '2026-08-01']);

        Livewire::actingAs($user)
            ->test(Stories::class)
            ->assertSeeInOrder(['حكاية مميزة', 'حكاية عادية']);
    }

    public function test_user_cannot_toggle_another_users_story(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $story = RecoveryStory::factory()->for($owner)->create();

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Livewire::actingAs($intruder)->test(Stories::class)->call('toggleFeatured', $story->id);
    }

    public function test_stories_index_is_paginated(): void
    {
        $user = User::factory()->create();

        // 13 stories: 12 fill page one, the oldest lands on page two.
        RecoveryStory::factory()->for($user)->create(['title' => 'الأقدم', 'date' => '2026-01-01']);
        foreach (range(1, 12) as $i) {
            RecoveryStory::factory()->for($user)->create([
                'title' => "حكاية رقم {$i}",
                'date' => '2026-02-'.str_pad((string) $i, 2, '0', STR_PAD_LEFT),
            ]);
        }

        Livewire::actingAs($user)
            ->test(Stories::class)
            ->assertSee('حكاية رقم 12')
            ->assertDontSee('الأقدم')
            ->call('gotoPage', 2)
            ->assertSee('الأقدم')
            ->assertDontSee('حكاية رقم 12');
    }
}
