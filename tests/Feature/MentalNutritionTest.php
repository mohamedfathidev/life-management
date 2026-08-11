<?php

namespace Tests\Feature;

use App\Livewire\Recovery\ManageTopic;
use App\Livewire\Recovery\MentalNutrition;
use App\Models\MentalNutritionLog;
use App\Models\RecoveryTopic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class MentalNutritionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2026-08-11 10:00:00');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_prompts_to_add_topics_when_none_exist(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(MentalNutrition::class)
            ->assertViewHas('hasTopics', false)
            ->assertSee('محتاج تكتب مواضيع الأول');
    }

    public function test_suggests_the_least_recently_read_topic(): void
    {
        $user = User::factory()->create();
        $read = RecoveryTopic::factory()->for($user)->create(['title' => 'مقروء']);
        $fresh = RecoveryTopic::factory()->for($user)->create(['title' => 'جديد']);

        MentalNutritionLog::factory()->for($user)->on('2026-08-06')
            ->create(['recovery_topic_id' => $read->id]);

        Livewire::actingAs($user)
            ->test(MentalNutrition::class)
            ->assertViewHas('suggested', fn ($t) => $t->id === $fresh->id);
    }

    public function test_importance_breaks_the_tie_for_never_read_topics(): void
    {
        $user = User::factory()->create();
        RecoveryTopic::factory()->for($user)->create(['importance' => 'low']);
        $high = RecoveryTopic::factory()->for($user)->create(['importance' => 'high']);

        Livewire::actingAs($user)
            ->test(MentalNutrition::class)
            ->assertViewHas('suggested', fn ($t) => $t->id === $high->id);
    }

    public function test_marking_consumed_records_todays_log(): void
    {
        $user = User::factory()->create();
        $topic = RecoveryTopic::factory()->for($user)->create();

        Livewire::actingAs($user)
            ->test(MentalNutrition::class)
            ->set('reflection', 'وصلني إن المساء خطر')
            ->call('markConsumed', $topic->id);

        $this->assertTrue(
            MentalNutritionLog::where('user_id', $user->id)
                ->whereDate('date', '2026-08-11')
                ->where('recovery_topic_id', $topic->id)
                ->exists()
        );
    }

    public function test_streak_counts_consecutive_days(): void
    {
        $user = User::factory()->create();
        $topic = RecoveryTopic::factory()->for($user)->create();

        foreach (['2026-08-11', '2026-08-10', '2026-08-09'] as $date) {
            MentalNutritionLog::factory()->for($user)->on($date)
                ->create(['recovery_topic_id' => $topic->id]);
        }

        Livewire::actingAs($user)
            ->test(MentalNutrition::class)
            ->assertViewHas('streak', 3);
    }

    public function test_topic_importance_is_saved(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ManageTopic::class)
            ->call('openForCreate')
            ->set('form.title', 'خطر المساء')
            ->set('form.importance', 'high')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('high', RecoveryTopic::first()->importance->value);
    }
}
