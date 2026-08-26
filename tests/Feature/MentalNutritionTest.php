<?php

namespace Tests\Feature;

use App\Enums\MentalNutritionSourceType;
use App\Livewire\Recovery\ManageTopic;
use App\Livewire\Recovery\MentalNutrition;
use App\Models\MentalNutritionLog;
use App\Models\RecoveryCommitment;
use App\Models\RecoveryDamage;
use App\Models\RecoveryHardMoment;
use App\Models\RecoveryIdea;
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

    public function test_prompts_to_add_content_when_no_source_has_anything(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(MentalNutrition::class)
            ->assertViewHas('hasItems', false)
            ->assertSee('محتاج تكتب حاجة في تابات التعافي');
    }

    public function test_suggests_the_least_recently_shown_item_regardless_of_source(): void
    {
        $user = User::factory()->create();
        $topic = RecoveryTopic::factory()->for($user)->create();
        $damage = RecoveryDamage::factory()->for($user)->create();

        MentalNutritionLog::factory()->for($user)->on('2026-08-06')
            ->ofSource(MentalNutritionSourceType::Topic, $topic->id)
            ->create();

        Livewire::actingAs($user)
            ->test(MentalNutrition::class)
            ->assertViewHas('suggested', fn ($item) => $item->type === MentalNutritionSourceType::Damage && $item->id === $damage->id);
    }

    public function test_suggestion_is_stable_for_the_same_day_when_multiple_items_are_untouched(): void
    {
        $user = User::factory()->create();
        RecoveryTopic::factory()->for($user)->count(3)->create();
        RecoveryDamage::factory()->for($user)->count(3)->create();

        $first = Livewire::actingAs($user)->test(MentalNutrition::class)->viewData('suggested');
        $second = Livewire::actingAs($user)->test(MentalNutrition::class)->viewData('suggested');

        $this->assertSame($first->type, $second->type);
        $this->assertSame($first->id, $second->id);
    }

    public function test_marking_consumed_records_todays_log_with_its_source(): void
    {
        $user = User::factory()->create();
        $damage = RecoveryDamage::factory()->for($user)->create();

        Livewire::actingAs($user)
            ->test(MentalNutrition::class)
            ->set('reflection', 'وصلني إن المساء خطر')
            ->call('markConsumed', 'damage', $damage->id);

        $this->assertTrue(
            MentalNutritionLog::where('user_id', $user->id)
                ->whereDate('date', '2026-08-11')
                ->where('source_type', MentalNutritionSourceType::Damage)
                ->where('source_id', $damage->id)
                ->exists()
        );
    }

    public function test_marking_consumed_ignores_content_that_does_not_belong_to_the_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $theirDamage = RecoveryDamage::factory()->for($other)->create();

        Livewire::actingAs($user)
            ->test(MentalNutrition::class)
            ->call('markConsumed', 'damage', $theirDamage->id);

        $this->assertFalse(
            MentalNutritionLog::where('user_id', $user->id)->whereDate('date', '2026-08-11')->exists()
        );
    }

    public function test_streak_counts_consecutive_days(): void
    {
        $user = User::factory()->create();
        $topic = RecoveryTopic::factory()->for($user)->create();

        foreach (['2026-08-11', '2026-08-10', '2026-08-09'] as $date) {
            MentalNutritionLog::factory()->for($user)->on($date)
                ->ofSource(MentalNutritionSourceType::Topic, $topic->id)
                ->create();
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

    public function test_pool_includes_the_newer_recovery_tabs(): void
    {
        $user = User::factory()->create();
        RecoveryHardMoment::factory()->for($user)->create();
        RecoveryIdea::factory()->for($user)->create();
        RecoveryCommitment::factory()->for($user)->create();

        $pool = (new \App\Services\MentalNutritionPoolService($user))->pool();

        $this->assertTrue($pool->contains(fn ($item) => $item->type === MentalNutritionSourceType::HardMoment));
        $this->assertTrue($pool->contains(fn ($item) => $item->type === MentalNutritionSourceType::Idea));
        $this->assertTrue($pool->contains(fn ($item) => $item->type === MentalNutritionSourceType::Commitment));
    }
}
