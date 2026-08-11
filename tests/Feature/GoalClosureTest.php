<?php

namespace Tests\Feature;

use App\Enums\GoalStatus;
use App\Livewire\Goals\CloseGoal;
use App\Livewire\Statistics\Index as StatisticsIndex;
use App\Models\Goal;
use App\Models\GoalReview;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class GoalClosureTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_goal_cannot_be_closed_before_its_last_day(): void
    {
        $goal = Goal::factory()->create([
            'target_date' => Carbon::today()->addDays(3),
        ]);

        $this->assertFalse($goal->canClose());
    }

    public function test_goal_can_be_closed_on_or_after_its_last_day(): void
    {
        $today = Goal::factory()->create(['target_date' => Carbon::today()]);
        $past = Goal::factory()->create(['target_date' => Carbon::today()->subDay()]);

        $this->assertTrue($today->canClose());
        $this->assertTrue($past->canClose());
    }

    public function test_sub_goal_cannot_be_closed_with_a_review(): void
    {
        $parent = Goal::factory()->create();
        $sub = Goal::factory()->childOf($parent)->create(['target_date' => Carbon::today()]);

        $this->assertFalse($sub->canClose());
    }

    public function test_closing_a_goal_stores_the_review_and_completes_it(): void
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->for($user)->create([
            'target_date' => Carbon::today(),
            'status' => GoalStatus::Active->value,
        ]);

        Livewire::actingAs($user)
            ->test(CloseGoal::class)
            ->call('openFor', $goal)
            ->set('shortcomings', ['أجّلت كتير', ''])
            ->set('strengths', ['التزمت بالمواعيد'])
            ->set('improvement', 65)
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('goal-saved');

        $goal->refresh();
        $this->assertSame(GoalStatus::Completed, $goal->status);

        $review = $goal->review;
        $this->assertNotNull($review);
        $this->assertSame(65, $review->improvement_percent);
        $this->assertSame(['أجّلت كتير'], $review->shortcomings); // empty filtered out
        $this->assertSame(['التزمت بالمواعيد'], $review->strengths);
    }

    public function test_closing_is_ignored_when_goal_is_not_yet_at_its_last_day(): void
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->for($user)->create([
            'target_date' => Carbon::today()->addDays(5),
        ]);

        Livewire::actingAs($user)
            ->test(CloseGoal::class)
            ->call('openFor', $goal)
            ->assertSet('open', false);
    }

    public function test_statistics_colors_improvement_against_the_previous_goal(): void
    {
        $user = User::factory()->create();

        $g1 = Goal::factory()->for($user)->create();
        $g2 = Goal::factory()->for($user)->create();

        // g1 closed earlier at 40%, g2 closed later at 70% → g2 trends "up"
        GoalReview::create([
            'user_id' => $user->id, 'goal_id' => $g1->id,
            'improvement_percent' => 40, 'closed_on' => Carbon::today()->subDays(10),
        ]);
        GoalReview::create([
            'user_id' => $user->id, 'goal_id' => $g2->id,
            'improvement_percent' => 70, 'closed_on' => Carbon::today(),
        ]);

        Livewire::actingAs($user)
            ->test(StatisticsIndex::class)
            ->assertViewHas('averageImprovement', 55)
            ->assertViewHas('rows', function ($rows) use ($g2, $g1) {
                // newest first
                $latest = $rows->first();
                $oldest = $rows->last();

                return $latest['review']->goal_id === $g2->id
                    && $latest['trend'] === 'up'
                    && $latest['delta'] === 30
                    && $oldest['review']->goal_id === $g1->id
                    && $oldest['trend'] === 'first';
            });
    }
}
